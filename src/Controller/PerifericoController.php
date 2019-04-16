<?php

namespace App\Controller;

use App\Form\PerifericoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Periferico;

/*
 * @Route("/periferico")
 */

/**
 * @Route({
 *     "en": "/peripheral",
 *     "es": "/periferico",
 *     "fr": "/peripherique"
 * })
 */
class PerifericoController extends Controller
{

    /**
     * @Route("/", name="periferico_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $perifericos = $this->getDoctrine()->getRepository(Periferico::class)->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('periferico/_table.html.twig', [
                'perifericos' => $perifericos,
            ]);

        return $this->render('periferico/index.html.twig', ['perifericos' => $perifericos]);
    }


    /**
     * @Route("/new", name="periferico_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $periferico = new Periferico();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PerifericoType::class, $periferico, array('action' => $this->generateUrl('periferico_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($periferico);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('peripheral_register_successfully'),
                    'nombre' => $periferico->getNombre(),
                    'tipo' => $this->get('translator')->trans('peripheral_type_'.$periferico->getTipo()),
                    'id' => $periferico->getId(),
                ));
            } else {
                $page = $this->renderView('periferico/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('periferico/_new.html.twig', [
            'periferico' => $periferico,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="periferico_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Periferico $periferico): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $form = $this->createForm(PerifericoType::class, $periferico, array('action' => $this->generateUrl('periferico_edit', array('id' => $periferico->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($periferico);
                $em->flush();
                return new JsonResponse(
                    array('mensaje' => $this->get('translator')->trans('peripheral_update_successfully'),
                        'nombre' => $periferico->getNombre(),
                        'tipo' => $this->get('translator')->trans('peripheral_type_'.$periferico->getTipo()),
                    ));
            } else {
                $page = $this->renderView('periferico/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'periferico_edit',
                    'action' => 'update_button',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true));
            }

        return $this->render('periferico/_new.html.twig', [
            'periferico' => $periferico,
            'title' => 'peripheral_edit_title',
            'action' => 'update_button',
            'form_id' => 'periferico_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show",options={"expose"=true}, name="periferico_show")
     */
    public function show(Request $request, Periferico $periferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('periferico/_show.html.twig',['periferico'=>$periferico]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="periferico_delete")
     */
    public function delete(Request $request, Periferico $periferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($periferico);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('peripheral_delete_successfully')));
    }

}
