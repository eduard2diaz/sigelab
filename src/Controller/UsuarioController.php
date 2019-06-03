<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Tool\FileStorageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route({
 *     "en": "/user",
 *     "es": "/usuario",
 *     "fr": "/utilisateur"
 * })
 */
class UsuarioController extends AbstractController
{
    /**
     * @Route("/", name="usuario_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $em=$this->getDoctrine()->getManager();
            $usuarios = $em->getRepository('App:Usuario')->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('usuario/_table.html.twig', ['usuarios' => $usuarios]);


        return $this->render('usuario/index.html.twig', ['usuarios' => $usuarios]);
    }

    /**
     * @Route("/new", name="usuario_new", methods="GET|POST")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario, array('action' => $this->generateUrl('usuario_new'), 'esAdmin' => $this->isGranted('ROLE_ADMIN'), 'disab' => false));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();
                return $this->json(array('mensaje' => $translator->trans('user_register_successfully'),
                    'nombre' => $usuario->getNombre(),
                    'apellido' => $usuario->getApellido(),
                    'usuario' => $usuario->getUsuario(),
                    'badge_texto' => $translator->trans($usuario->getActivo() ? 'yes' : 'no'),
                    'badge_color' => $usuario->getActivo() ? 'success' : 'danger',
                    'id' => $usuario->getId(),
                ));
            } else {
                $page = $this->renderView('usuario/_form.html.twig', array(
                    'form' => $form->createView(),
                    'usuario' => $usuario,
                ));
                return $this->json(array('form' => $page, 'error' => true));
            }


        return $this->render('usuario/_new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Usuario $usuario, TranslatorInterface $translator,  UserPasswordEncoderInterface $encoder): Response
    {
        if (!$request->isXmlHttpRequest() )
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$usuario);
        $disabled = $this->getUser()->getId() == $usuario->getId();
        $form = $this->createForm(UsuarioType::class, $usuario, array('action' => $this->generateUrl('usuario_edit', array('id' => $usuario->getId())), 'esAdmin' => $this->isGranted('ROLE_ADMIN'), 'disab' => $disabled));
        $passwordOriginal = $form->getData()->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if (null == $usuario->getPassword())
                    $usuario->setPassword($passwordOriginal);
                else{
                    $newpassword=$encoder->encodePassword($usuario,$usuario->getPassword());
                    $usuario->setPassword($newpassword);
                }

                $ruta = $this->getParameter('storage_directory');
                if ($usuario->getFile() != null) {
                    if ($usuario->getRutaFoto() != null){
                        $rutaArchivo = $ruta . DIRECTORY_SEPARATOR . $usuario->getRutaFoto();
                        FileStorageManager::removeUpload($rutaArchivo);
                    }
                    $usuario->setRutaFoto(FileStorageManager::Upload($ruta,$usuario->getFile()));
                    $usuario->setFile(null);
                }

                $em->persist($usuario);
                $em->flush();
                return $this->json(array('mensaje' => $translator->trans('user_update_successfully'),
                    'nombre' => $usuario->getNombre(),
                    'apellidos' => $usuario->getApellido(),
                    'usuario' => $usuario->getUsuario(),
                    'badge_texto' => $translator->trans($usuario->getActivo() ? 'yes' : 'no'),
                    'badge_color' => $usuario->getActivo() ? 'success' : 'danger',
                   ));
            } else {
                $page = $this->renderView('usuario/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'usuario_edit',
                    'usuario' => $usuario,
                ));
                return $this->json(array('form' => $page, 'error' => true));
            }

        return $this->render('usuario/_new.html.twig', [
            'usuario' => $usuario,
            'title' => 'user_edit_title',
            'action' => 'update_button',
            'form_id' => 'usuario_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="usuario_show", options={"expose"=true}, methods="GET")
     */
    public function show(Request $request, Usuario $usuario): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('usuario/_show.html.twig', ['usuario' => $usuario]);
    }

    /**
     * @Route("/{id}/delete", name="usuario_delete",options={"expose"=true})
     */
    public function delete(Request $request, Usuario $usuario, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$usuario);
        $em = $this->getDoctrine()->getManager();
        $em->remove($usuario);
        $em->flush();

        return $this->json(array('mensaje' => $translator->trans('user_delete_successfully')));
    }

    /**
     * @Route("/ajax", name="usuario_ajax", options={"expose"=true})
     */
    public function ajax(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $result=[];
        if($request->get('q')!=null) {
            $em = $this->getDoctrine()->getManager();
            $parameter = $request->get('q');
            $query = $em->createQuery('SELECT u.id, u.nombre as text FROM App:Usuario u WHERE upper(u.nombre) LIKE :nombre ORDER BY u.nombre ASC')
                ->setParameter('nombre', '%' . strtoupper($parameter) . '%');
            $result = $query->getResult();
            return new Response(json_encode($result));
        }
        return new Response(json_encode($result));
    }

}
