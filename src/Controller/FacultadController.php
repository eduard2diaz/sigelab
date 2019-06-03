<?php

namespace App\Controller;

use App\Form\FacultadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Facultad;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route({
 *     "en": "/faculty",
 *     "es": "/facultad",
 *     "fr": "/moyen",
 * })
 */
class FacultadController extends AbstractController
{

    /**
     * @Route("/", name="facultad_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $facultads = $this->getDoctrine()->getRepository(Facultad::class)->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('facultad/_table.html.twig', [
                'facultades' => $facultads,
            ]);

        return $this->render('facultad/index.html.twig', ['facultades' => $facultads]);
    }


    /**
     * @Route("/new", name="facultad_new", methods="GET|POST")
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $facultad = new Facultad();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(FacultadType::class, $facultad, array('action' => $this->generateUrl('facultad_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($facultad);
                $em->flush();
                $message=$translator->trans('faculty_register_successfully');

                return $this->json(array('mensaje' => $message,
                    'nombre' => $facultad->getNombre(),
                    'id' => $facultad->getId(),
                ));
            } else {
                $page = $this->renderView('facultad/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return $this->json(array('form' => $page, 'error' => true,));
            }


        return $this->render('facultad/_new.html.twig', [
            'facultad' => $facultad,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="facultad_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Facultad $facultad, TranslatorInterface $translator): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $form = $this->createForm(FacultadType::class, $facultad, array('action' => $this->generateUrl('facultad_edit', array('id' => $facultad->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($facultad);
                $em->flush();
                $message=$translator->trans('faculty_update_successfully');
                return $this->json(array('mensaje' => $message, 'nombre' => $facultad->getNombre()));
            } else {
                $page = $this->renderView('facultad/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'facultad_edit',
                    'action' => 'update_button',
                ));
                return $this->json(array('form' => $page, 'error' => true));
            }

        return $this->render('facultad/_new.html.twig', [
            'facultad' => $facultad,
            'title' => 'faculty_edit_title',
            'action' => 'update_button',
            'form_id' => 'facultad_edit',
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/show",options={"expose"=true}, name="facultad_show")
     */
    public function show(Request $request, Facultad $facultad): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('facultad/_show.html.twig',['facultad'=>$facultad]);
    }


    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="facultad_delete")
     */
    public function delete(Request $request, Facultad $facultad, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($facultad);
        $em->flush();
        return $this->json(array('mensaje' => $translator->trans('faculty_delete_successfully')));
    }

}
