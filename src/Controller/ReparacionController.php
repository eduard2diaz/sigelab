<?php

namespace App\Controller;

use App\Form\ReparacionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Reparacion;
use App\Entity\Pc;
/*
 * @Route("/reparacion")
 */

/**
 * @Route({
 *     "en": "/reparation",
 *     "es": "/reparacion",
 *     "fr": "/reparer"
 * })
 */
class ReparacionController extends Controller
{

    /**
     * @Route("/{id}/index", name="reparacion_index", methods="GET")
     */
    public function index(Request $request,Pc $pc): Response
    {
        $this->denyAccessUnlessGranted('VIEW',$pc);
        $reparacions = $this->getDoctrine()->getRepository(Reparacion::class)->findBy(
            array('pc'=>$pc),
            array('fecha'=>'DESC')
        );
        if($request->isXmlHttpRequest())
            return $this->render('reparacion/index.html.twig', ['reparaciones' => $reparacions,'pc'=>$pc->getId()]);
        else
            return $this->render('reparacion/_table.html.twig', ['reparaciones' => $reparacions,'pc'=>$pc->getId(),'readonly'=>true]);
    }


    /**
     * @Route("/{id}/new", name="reparacion_new", methods="GET|POST")
     */
    public function new(Request $request,Pc $pc): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('NEW',$pc);
        $reparacion = new Reparacion();
        $reparacion->setPc($pc);
        $reparacion->setUsuario($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ReparacionType::class, $reparacion, array('action' => $this->generateUrl('reparacion_new',array('id'=>$pc->getId()))));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($reparacion);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reparationpc_register_successfully'),
                    'fecha' => $reparacion->getFecha()->format('d-m-Y'),
                    'tiporeparacion' => $reparacion->getTiporeparacion()->getNombre(),
                    'usuario' => $reparacion->getUsuario()->__toString(),
                    'id' => $reparacion->getId(),
                ));
            } else {
                $page = $this->renderView('reparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('reparacion/_new.html.twig', [
            'reparacion' => $reparacion,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="reparacion_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Reparacion $reparacion): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $this->denyAccessUnlessGranted('NEW',$reparacion->getPc());
        $form = $this->createForm(ReparacionType::class, $reparacion, array('action' => $this->generateUrl('reparacion_edit', array('id' => $reparacion->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $reparacion->setUsuario($this->getUser());
                $em->persist($reparacion);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reparationpc_update_successfully'),
                    'fecha' => $reparacion->getFecha()->format('d-m-Y'),
                    'tiporeparacion' => $reparacion->getTiporeparacion()->getNombre(),
                    'usuario' => $reparacion->getUsuario()->__toString(),
                    ));
            } else {
                $page = $this->renderView('reparacion/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'reparacion_edit',
                    'action' => 'update_button',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true));
            }

        return $this->render('reparacion/_new.html.twig', [
            'reparacion' => $reparacion,
            'title' => 'reparationpc_edit_title',
            'action' => 'update_button',
            'form_id' => 'reparacion_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show",options={"expose"=true}, name="reparacion_show")
     */
    public function show(Request $request, Reparacion $reparacion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$reparacion->getPc());
        return $this->render('reparacion/_show.html.twig',['reparacion'=>$reparacion]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="reparacion_delete")
     */
    public function delete(Request $request, Reparacion $reparacion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$reparacion->getPc());
        $em = $this->getDoctrine()->getManager();
        $em->remove($reparacion);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reparationpc_delete_successfully')));
    }

}
