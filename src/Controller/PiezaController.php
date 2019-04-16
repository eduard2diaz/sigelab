<?php

namespace App\Controller;

use App\Form\PiezaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pieza;
use App\Entity\Periferico;

/*
 * @Route("/pieza")
 */

/**
 * @Route({
 *     "en": "/piece",
 *     "es": "/pieza",
 *     "fr": "/pièce",
 * } ,options={"utf8": true})
 */
class PiezaController extends Controller
{

    /**
     * @Route("/", name="pieza_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $piezas = $this->getDoctrine()->getRepository(Pieza::class)->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('pieza/_table.html.twig', [
                'piezas' => $piezas,
            ]);

        return $this->render('pieza/index.html.twig', ['piezas' => $piezas]);
    }


    /**
     * @Route("/new", name="pieza_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $pieza = new Pieza();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PiezaType::class, $pieza, array('action' => $this->generateUrl('pieza_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($pieza);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_register_successfully'),
                    'nombre' => $pieza->getNombre(),
                    'id' => $pieza->getId(),
                ));
            } else {
                $page = $this->renderView('pieza/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('pieza/_new.html.twig', [
            'pieza' => $pieza,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="pieza_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Pieza $pieza): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $form = $this->createForm(PiezaType::class, $pieza, array('action' => $this->generateUrl('pieza_edit', array('id' => $pieza->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($pieza);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_update_successfully'), 'nombre' => $pieza->getNombre()));
            } else {
                $page = $this->renderView('pieza/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'pieza_edit',
                    'action' => 'update_button',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true));
            }

        return $this->render('pieza/_new.html.twig', [
            'pieza' => $pieza,
            'title' => 'piece_edit_title',
            'action' => 'update_button',
            'form_id' => 'pieza_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show",options={"expose"=true}, name="pieza_show")
     */
    public function show(Request $request, Pieza $pieza): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('pieza/_show.html.twig',['pieza'=>$pieza]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="pieza_delete")
     */
    public function delete(Request $request, Pieza $pieza): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($pieza);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('piece_delete_successfully')));
    }

    /**
     * @Route("/{periferico}/searchbypieza",options={"expose"=true}, name="pieza_searchbyperiferico")
     */
    public function searchbypieza(Request $request, Periferico $periferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $array=array();
        foreach ($periferico->getIdpieza()->toArray() as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getNombre()];

        return new Response(json_encode($array));
    }

}
