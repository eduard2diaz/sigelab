<?php

namespace App\Controller;

use App\Entity\Pc;
use App\Entity\RegistroPieza;
use App\Form\RegistroPiezaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route({
 *     "en": "/pieceregister",
 *     "es": "/registropieza",
 *     "fr": "/piecededisque",
 * })
 */
class RegistroPiezaController extends Controller
{
    /**
     * @Route("/{pc}/index", name="registropieza_index", methods="GET")
     */
    public function index(Request $request,Pc $pc): Response
    {

        $this->denyAccessUnlessGranted('VIEW',$pc);
        $registropiezas = $this->getDoctrine()
            ->getRepository(RegistroPieza::class)
            ->findBy(array('pc'=>$pc));
        if ($request->isXmlHttpRequest())
        return $this->render('registropieza/index.html.twig', ['pc'=>$pc,'registros' => $registropiezas]);
        else
            return $this->render('registropieza/_table.html.twig', ['pc'=>$pc,'registros' => $registropiezas,'readonly'=>true]);
    }

    /**
     * @Route("/{pc}/new", name="registropieza_new", methods="GET|POST")
     */
    public function new(Request $request,Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$pc);
        $registropieza = new RegistroPieza();
        $registropieza->setPc($pc);
        $form = $this->createForm(RegistroPiezaType::class, $registropieza,array('action'=>$this->generateUrl('registropieza_new',array('pc'=>$pc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
                $em->persist($registropieza);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_register_successfully'),
                    'pieza' => $registropieza->getPieza()->getNombre(),
                    'propiedad' => $registropieza->getPropiedad()->getNombre(),
                    'valor' => $registropieza->getValor(),
                    'id' => $registropieza->getId(),
                ));
        }else{
                $page = $this->renderView('registropieza/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registropieza/_new.html.twig', [
            'registropieza' => $registropieza,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="registropieza_edit",options={"expose"=true} , methods="GET|POST")
     */
    public function edit(Request $request, RegistroPieza $registropieza): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$registropieza->getPc());
        $form = $this->createForm(RegistroPiezaType::class, $registropieza,array('action'=>$this->generateUrl('registropieza_edit',array('id'=>$registropieza->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_update_successfully'),
                    'propiedad' => $registropieza->getPropiedad()->getNombre(),
                    'pieza' => $registropieza->getPieza()->getNombre(),
                    'valor' => $registropieza->getValor(),
                ));
            }else{
                $page = $this->renderView('registropieza/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'registropieza_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registropieza/_new.html.twig', [
            'registropieza' => $registropieza,
            'form' => $form->createView(),
            'title' => 'piece_edit_title',
            'action' => 'update_button',
            'form_id' => 'registropieza_edit',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="registropieza_delete",options={"expose"=true})
     */
    public function delete(Request $request, RegistroPieza $registropieza): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('VIEW',$registropieza->getPc());
        $em = $this->getDoctrine()->getManager();
        $em->remove($registropieza);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_delete_successfully')));
    }
}
