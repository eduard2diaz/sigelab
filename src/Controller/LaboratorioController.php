<?php

namespace App\Controller;

use App\Form\LaboratorioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Laboratorio;

/*
 * @Route("/laboratorio")
 */

/**
 * @Route({
 *     "en": "/laboratory",
 *     "es": "/laboratorio",
 *     "fr": "/laboratoire",
 * })
 */
class LaboratorioController extends Controller
{

    /**
     * @Route("/", name="laboratorio_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        if ($this->isGranted('ROLE_ADMIN'))
            $laboratorios = $this->getDoctrine()->getRepository(Laboratorio::class)->findAll();
        else
            $laboratorios = $this->getUser()->getFacultad()->getIdlaboratorio()->toArray();

        if ($request->isXmlHttpRequest())
            return $this->render('laboratorio/_table.html.twig', [
                'laboratorios' => $laboratorios,
            ]);

        return $this->render('laboratorio/index.html.twig', ['laboratorios' => $laboratorios]);
    }


    /**
     * @Route("/new", name="laboratorio_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $laboratorio = new Laboratorio();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(LaboratorioType::class, $laboratorio, array('action' => $this->generateUrl('laboratorio_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($laboratorio);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('laboratory_register_successfully'),
                    'nombre' => $laboratorio->getNombre(),
                    'badge_texto' => $this->get('translator')->trans($laboratorio->getEnfuncionamiento() ? 'yes' : 'no'),
                    'badge_color' => $laboratorio->getEnfuncionamiento() ? 'success' : 'danger',
                    'facultad' => $laboratorio->getFacultad()->getNombre(),
                    'id' => $laboratorio->getId(),
                ));
            } else {
                $page = $this->renderView('laboratorio/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('laboratorio/_new.html.twig', [
            'laboratorio' => $laboratorio,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="laboratorio_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Laboratorio $laboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$laboratorio);
        $form = $this->createForm(LaboratorioType::class, $laboratorio, array('esAdmin' => $this->isGranted('ROLE_ADMIN'), 'action' => $this->generateUrl('laboratorio_edit', array('id' => $laboratorio->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($laboratorio);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('laboratory_update_successfully'),
                    'badge_texto' => $this->get('translator')->trans($laboratorio->getEnfuncionamiento() ? 'yes' : 'no'),
                    'badge_color' => $laboratorio->getEnfuncionamiento() ? 'success' : 'danger',
                    'facultad' => $laboratorio->getFacultad()->getNombre(),));
            } else {
                $page = $this->renderView('laboratorio/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'laboratorio_edit',
                    'action' => 'update_button',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true));
            }

        return $this->render('laboratorio/_new.html.twig', [
            'laboratorio' => $laboratorio,
            'title' => 'laboratory_edit_title',
            'action' => 'update_button',
            'form_id' => 'laboratorio_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show",options={"expose"=true}, name="laboratorio_show")
     */
    public function show(Request $request, Laboratorio $laboratorio): Response
    {
        $this->denyAccessUnlessGranted('EDIT',$laboratorio);
        $em = $this->getDoctrine()->getManager();
        $pcs = $em->getRepository('App:Pc')->findBy(array('laboratorio' => $laboratorio));
        if ($request->isXmlHttpRequest())
            return $this->render('pc/_table.html.twig', [
                'pcs' => $pcs,
            ]);

        return $this->render('pc/index.html.twig', ['pcs' => $pcs, 'laboratorio' => $laboratorio]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="laboratorio_delete")
     */
    public function delete(Request $request, Laboratorio $laboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($laboratorio);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('laboratory_delete_successfully')));
    }
}
