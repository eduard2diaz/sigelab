<?php

namespace App\Controller;

use App\Entity\Pc;
use App\Entity\RegistroPropiedad;
use App\Form\RegistroPropiedadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * @Route("/registropropiedad")
 */

/**
 * @Route({
 *     "en": "/propertyregister",
 *     "es": "/registropropiedad",
 *     "fr": "/proprietedenregistrement"
 * })
 */
class RegistroPropiedadController extends Controller
{
    /**
     * @Route("/{pc}/index", name="registropropiedad_index", methods="GET")
     */
    public function index(Request $request, Pc $pc): Response
    {

        $this->denyAccessUnlessGranted('VIEW', $pc);
        $registropropiedads = $this->getDoctrine()
            ->getRepository(RegistroPropiedad::class)
            ->findBy(array('pc' => $pc));

        if ($request->isXmlHttpRequest())
            return $this->render('registropropiedad/index.html.twig', ['pc' => $pc,'registros' => $registropropiedads]);
        else
            return $this->render('registropropiedad/_table.html.twig', ['pc' => $pc,'registros' => $registropropiedads,'readonly'=>true]);
    }

    /**
     * @Route("/{pc}/new", name="registropropiedad_new", methods="GET|POST")
     */
    public function new(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $pc);
        $registropropiedad = new RegistroPropiedad();
        $registropropiedad->setPc($pc);
        $form = $this->createForm(RegistroPropiedadType::class, $registropropiedad, array('action' => $this->generateUrl('registropropiedad_new', array('pc' => $pc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($registropropiedad);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('property_register_successfully'),
                    'propiedad' => $registropropiedad->getPropiedad()->getNombre(),
                    'valor' => $registropropiedad->getValor(),
                    'id' => $registropropiedad->getId(),
                ));
            } else {
                $page = $this->renderView('registropropiedad/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registropropiedad/_new.html.twig', [
            'registropropiedad' => $registropropiedad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="registropropiedad_edit",options={"expose"=true} , methods="GET|POST")
     */
    public function edit(Request $request, RegistroPropiedad $registropropiedad): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $registropropiedad->getPc());
        $form = $this->createForm(RegistroPropiedadType::class, $registropropiedad, array('action' => $this->generateUrl('registropropiedad_edit', array('id' => $registropropiedad->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('property_update_successfully'),
                    'propiedad' => $registropropiedad->getPropiedad()->getNombre(),
                    'valor' => $registropropiedad->getValor(),
                ));
            } else {
                $page = $this->renderView('registropropiedad/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'registropropiedad_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));

            }

        return $this->render('registropropiedad/_new.html.twig', [
            'registropropiedad' => $registropropiedad,
            'form' => $form->createView(),
            'title' => 'property_edit_title',
            'action' => 'update_button',
            'form_id' => 'registropropiedad_edit',
        ]);
    }

    /**
     * @Route("/{id}/delete", name="registropropiedad_delete",options={"expose"=true})
     */
    public function delete(Request $request, RegistroPropiedad $registropropiedad): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW', $registropropiedad->getPc());
        $em = $this->getDoctrine()->getManager();
        $em->remove($registropropiedad);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('property_delete_successfully')));
    }
}
