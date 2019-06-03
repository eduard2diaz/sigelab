<?php

namespace App\Controller;

use App\Entity\Pc;
use App\Entity\RegistroPeriferico;
use App\Form\RegistroPerifericoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route({
 *     "en": "/peripheralregister",
 *     "es": "/registroperiferico",
 *     "fr": "/enregistrementperipherique"
 * })
 */
class RegistroPerifericoController extends Controller
{
    /**
     * @Route("/{pc}/index", name="registroperiferico_index", methods="GET")
     */
    public function index(Request $request, Pc $pc): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $pc);
        $registroperifericos = $this->getDoctrine()
            ->getRepository(RegistroPeriferico::class)
            ->findBy(array('pc' => $pc));

        if ($request->isXmlHttpRequest())
            return $this->render('registroperiferico/index.html.twig', ['pc' => $pc, 'registros' => $registroperifericos]);
        else
            return $this->render('registroperiferico/_table.html.twig', ['pc' => $pc, 'registros' => $registroperifericos,'readonly'=>true]);
    }

    /**
     * @Route("/{pc}/new", name="registroperiferico_new", methods="GET|POST")
     */
    public function new(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $pc);
        $registroperiferico = new RegistroPeriferico();
        $registroperiferico->setPc($pc);
        $form = $this->createForm(RegistroPerifericoType::class, $registroperiferico, array('action' => $this->generateUrl('registroperiferico_new', array('pc' => $pc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($registroperiferico);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('peripheral_register_successfully'),
                    'periferico' => $registroperiferico->getPeriferico()->getNombre(),
                    'pieza' => $registroperiferico->getPieza() ? $registroperiferico->getPieza()->getNombre() : '',
                    'propiedad' => $registroperiferico->getPropiedad()->getNombre(),
                    'valor' => $registroperiferico->getValor(),
                    'id' => $registroperiferico->getId(),
                ));
            } else {
                $page = $this->renderView('registroperiferico/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registroperiferico/_new.html.twig', [
            'registroperiferico' => $registroperiferico,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="registroperiferico_edit",options={"expose"=true} , methods="GET|POST")
     */
    public function edit(Request $request, RegistroPeriferico $registroperiferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $registroperiferico->getPc());
        $form = $this->createForm(RegistroPerifericoType::class, $registroperiferico, array('action' => $this->generateUrl('registroperiferico_edit', array('id' => $registroperiferico->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('peripheral_update_successfully'),
                    'pieza' => $registroperiferico->getPieza() ? $registroperiferico->getPieza()->getNombre() : '',
                    'propiedad' => $registroperiferico->getPropiedad() ? $registroperiferico->getPropiedad()->getNombre() : '',
                    'periferico' => $registroperiferico->getPeriferico()->getNombre(),
                    'valor' => $registroperiferico->getValor(),
                ));
            } else {
                $page = $this->renderView('registroperiferico/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'registroperiferico_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registroperiferico/_new.html.twig', [
            'registroperiferico' => $registroperiferico,
            'form' => $form->createView(),
            'title' => 'peripheral_edit_title',
            'action' => 'update_button',
            'form_id' => 'registroperiferico_edit',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="registroperiferico_delete",options={"expose"=true})
     */
    public function delete(Request $request, RegistroPeriferico $registroperiferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $registroperiferico->getPc());

        $em = $this->getDoctrine()->getManager();
        $em->remove($registroperiferico);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('peripheral_delete_successfully')));
    }
}
