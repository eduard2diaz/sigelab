<?php

namespace App\Controller;

use App\Entity\ReservacionLaboratorio;
use App\Form\ReservacionLaboratorioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * @Route("/reservacion/laboratorio")
 */

/**
 * @Route({
 *     "en": "/reservation/laboratory",
 *     "es": "/reservacion/laboratorio",
 *     "fr": "/reservation/laboratoire"
 * })
 */
class ReservacionLaboratorioController extends Controller
{
    /**
     * @Route("/", name="reservacion_laboratorio_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $misLaboratorios = $this->getUser()->getFacultad()->getIdlaboratorio();
        $em = $this->getDoctrine()->getManager();

            $consulta = $em->createQuery('SELECT r FROM App:ReservacionLaboratorio r JOIN r.laboratorio l WHERE l.id IN (:laboratorio)');
            $consulta->setParameter('laboratorio', $misLaboratorios);
            $result = $consulta->getResult();
            if($request->isXmlHttpRequest())
                return $this->render('reservacion_laboratorio/_table.html.twig', ['reservaciones' => $result]);

        return $this->render('reservacion_laboratorio/index.html.twig', ['reservaciones' => $result]);
    }

    /**
     * @Route("/new", name="reservacion_laboratorio_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
    //    if (!$request->isXmlHttpRequest())
    //        throw $this->createAccessDeniedException();

        $reservacionLaboratorio = new ReservacionLaboratorio();
        $reservacionLaboratorio->setUsuario($this->getUser());
        $misLaboratorios = $this->getUser()->getFacultad()->getIdlaboratorio()->toArray();

        $form = $this->createForm(ReservacionLaboratorioType::class, $reservacionLaboratorio, array('laboratorios' => $misLaboratorios, 'action' => $this->generateUrl('reservacion_laboratorio_new')));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($reservacionLaboratorio);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_register_successfully'),
                    'fechainicio' => $reservacionLaboratorio->getFechainicio()->format('d-m-Y H:i'),
                    'fechafin' => $reservacionLaboratorio->getFechafin()->format('d-m-Y H:i'),
                    'laboratorio' => $reservacionLaboratorio->getLaboratorio()->getNombre(),
                    'profesor' => $reservacionLaboratorio->getUsuario()->__toString(),
                    'id' => $reservacionLaboratorio->getId(),
                ));
            } else {
                $page = $this->renderView('reservacion_laboratorio/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('reservacion_laboratorio/_new.html.twig', [
            'reservacion_laboratorio' => $reservacionLaboratorio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservacion_laboratorio_edit",options={"expose"=true} , methods="GET|POST")
     */
    public function edit(Request $request, ReservacionLaboratorio $reservacionLaboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT', $reservacionLaboratorio);
        $misLaboratorios = $this->getUser()->getFacultad()->getIdlaboratorio()->toArray();
        $form = $this->createForm(ReservacionLaboratorioType::class, $reservacionLaboratorio, array('laboratorios' => $misLaboratorios, 'action' => $this->generateUrl('reservacion_laboratorio_edit', array('id' => $reservacionLaboratorio->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($reservacionLaboratorio);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_update_successfully'),
                    'fechainicio' => $reservacionLaboratorio->getFechainicio()->format('d-m-Y H:i'),
                    'fechafin' => $reservacionLaboratorio->getFechafin()->format('d-m-Y H:i'),
                    'laboratorio' => $reservacionLaboratorio->getLaboratorio()->getNombre(),
                    'profesor' => $reservacionLaboratorio->getUsuario()->__toString(),
                ));
            } else {
                $page = $this->renderView('reservacion_laboratorio/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'reservacionlaboratorio_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('reservacion_laboratorio/_new.html.twig', [
            'reservacion_laboratorio' => $reservacionLaboratorio,
            'title' => 'reservation_edit_title',
            'action' => 'update_button',
            'form_id' => 'reservacionlaboratorio_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservacion_laboratorio_delete",options={"expose"=true} )
     */
    public function delete(Request $request, ReservacionLaboratorio $reservacionLaboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE', $reservacionLaboratorio);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reservacionLaboratorio);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_delete_successfully')));
    }
}
