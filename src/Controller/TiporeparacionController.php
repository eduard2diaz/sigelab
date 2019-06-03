<?php

namespace App\Controller;

use App\Form\TiporeparacionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tiporeparacion;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route({
 *     "en": "/typereparation",
 *     "es": "/tiporeparacion",
 *     "fr": "/typeréparation"
 * }, options={"utf8": true})
 */
class TiporeparacionController extends AbstractController
{

    /**
     * @Route("/", name="tiporeparacion_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $tiporeparacions = $this->getDoctrine()->getRepository(Tiporeparacion::class)->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('tiporeparacion/_table.html.twig', [
                'tiporeparaciones' => $tiporeparacions,
            ]);

        return $this->render('tiporeparacion/index.html.twig', ['tiporeparaciones' => $tiporeparacions]);
    }


    /**
     * @Route("/new", name="tiporeparacion_new", methods="GET|POST")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tiporeparacion = new Tiporeparacion();
        $form = $this->createForm(TiporeparacionType::class, $tiporeparacion, array('action' => $this->generateUrl('tiporeparacion_new')));
        $form->handleRequest($request);
        
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tiporeparacion);
                $em->flush();
                return $this->json(
                    [
                        'mensaje' => $translator->trans('typeofrepair_register_successfully'),
                        'nombre' => $tiporeparacion->getNombre(),
                        'id' => $tiporeparacion->getId(),
                    ]
                );
            } else {
                $page = $this->renderView('tiporeparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return $this->json(['form' => $page, 'error' => true]);
            }


        return $this->render('tiporeparacion/_new.html.twig', [
            'tiporeparacion' => $tiporeparacion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="tiporeparacion_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Tiporeparacion $tiporeparacion, TranslatorInterface $translator): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $form = $this->createForm(TiporeparacionType::class, $tiporeparacion, array('action' => $this->generateUrl('tiporeparacion_edit', array('id' => $tiporeparacion->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tiporeparacion);
                $em->flush();
                return $this->json(
                    [
                        'mensaje' => $translator->trans('typeofrepair_update_successfully'),
                        'nombre' => $tiporeparacion->getNombre()
                    ]);
            } else {
                $page = $this->renderView('tiporeparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tiporeparacion_edit',
                    'action' => 'update_button',
                ));
                return $this->json(array('form' => $page, 'error' => true));
            }

        return $this->render('tiporeparacion/_new.html.twig', [
            'tiporeparacion' => $tiporeparacion,
            'title' => 'typeofrepair_edit_title',
            'action' => 'update_button',
            'form_id' => 'tiporeparacion_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="tiporeparacion_delete")
     */
    public function delete(Request $request, Tiporeparacion $tiporeparacion, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($tiporeparacion);
        $em->flush();
        return $this->json(array('mensaje' => $translator->trans('typeofrepair_delete_successfully')));
    }

}
