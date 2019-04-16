<?php

namespace App\Controller;

use App\Entity\ReservacionPc;
use App\Form\ReservacionPcType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Pc;

/*
 * @Route("/reservacion/pc")
 */

/**
 * @Route({
 *     "en": "/reservation/pc",
 *     "es": "/reservacion/computadora",
 *     "fr": "/reservation/calculateur"
 * })
 */
class ReservacionPcController extends Controller
{
    /**
     * @Route("/", name="reservacion_pc_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $misLaboratorios = $this->getUser()->getFacultad()->getIdlaboratorio();
        $em = $this->getDoctrine()->getManager();

        $consulta = $em->createQuery('SELECT r FROM App:ReservacionPc r JOIN r.pc pc JOIN pc.laboratorio l WHERE l IN (:laboratorio)');
        $consulta->setParameter('laboratorio', $misLaboratorios);
        $result = $consulta->getResult();
        if ($request->isXmlHttpRequest())
            return $this->render('reservacion_pc/_table.html.twig', ['reservaciones' => $result]);

        return $this->render('reservacion_pc/index.html.twig', ['reservaciones' => $result]);
    }

    /**
     * @Route("/new", name="reservacion_pc_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
          if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $reservacionPc = new ReservacionPc();
        $reservacionPc->setUsuario($this->getUser());
        $misLaboratorios = $this->obtenerLaboratorioDisponible();

        $form = $this->createForm(ReservacionPcType::class, $reservacionPc, array('laboratorios' => $misLaboratorios, 'action' => $this->generateUrl('reservacion_pc_new')));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pc = $this->obtenerComputadora($reservacionPc->getFecha(), $form->get('laboratorio')->getData(),null);
            if (!$pc)
                $form->get('laboratorio')->addError(new FormError($this->get('translator')->trans('reservation_error_noavailablepc')));
            else
                $reservacionPc->setPc($pc);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($reservacionPc);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_register_successfully'),
                    'fecha' => $reservacionPc->getFecha()->format('d-m-Y'),
                    'pc' => $reservacionPc->getPc()->getNumero(),
                    'laboratorio' => $reservacionPc->getPc()->getLaboratorio()->getNombre(),
                    'estudiante' => $reservacionPc->getUsuario()->__toString(),
                    'id' => $reservacionPc->getId(),
                ));
            } else {
                $page = $this->renderView('reservacion_pc/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }
        }
        return $this->render('reservacion_pc/_new.html.twig', [
            'reservacion_pc' => $reservacionPc,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservacion_pc_edit",options={"expose"=true} , methods="GET|POST")
     */
    public function edit(Request $request, ReservacionPc $reservacionPc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT', $reservacionPc);
        $misLaboratorios = $this->obtenerLaboratorioDisponible();
        $misLaboratorios[]=$reservacionPc->getPc()->getLaboratorio();
        $form = $this->createForm(ReservacionPcType::class, $reservacionPc, array('laboratorios' => $misLaboratorios, 'action' => $this->generateUrl('reservacion_pc_edit', array('id' => $reservacionPc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pc = $this->obtenerComputadora($reservacionPc->getFecha(), $form->get('laboratorio')->getData(),$reservacionPc->getId());
            if (!$pc)
                $form->get('laboratorio')->addError(new FormError($this->get('translator')->trans('reservation_error_noavailablepc')));
            else
                $reservacionPc->setPc($pc);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($reservacionPc);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_update_successfully'),
                    'fecha' => $reservacionPc->getFecha()->format('d-m-Y'),
                    'pc' => $reservacionPc->getPc()->getNumero(),
                    'laboratorio' => $reservacionPc->getPc()->getLaboratorio()->getNombre(),
                    'estudiante' => $reservacionPc->getUsuario()->__toString(),
                ));
            } else {
                $page = $this->renderView('reservacion_pc/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'reservacion_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }
        }

        return $this->render('reservacion_pc/_new.html.twig', [
            'reservacion_pc' => $reservacionPc,
            'title' => 'reservation_edit_title',
            'action' => 'update_button',
            'form_id' => 'reservacionpc_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservacion_pc_delete",options={"expose"=true} )
     */
    public function delete(Request $request, ReservacionPc $reservacionPc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE', $reservacionPc);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reservacionPc);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('reservation_delete_successfully')));
    }

    private function obtenerLaboratorioDisponible():array{
        $misLaboratorios = $this->getUser()->getFacultad()->getIdlaboratorio();
        $laboratoriosDisponibles=[];
        foreach ($misLaboratorios as $laboratorio) {
            if($this->obtenerComputadora(new \DateTime(),$laboratorio,null) instanceof  Pc)
                $laboratoriosDisponibles[]=$laboratorio;
        }
        return $laboratoriosDisponibles;
    }

    private function obtenerComputadora($fecha, $laboratorio, $reservacion_id): ?Pc
    {
        $em = $this->getDoctrine()->getManager();
        $laboratorio = $em->getRepository('App:Laboratorio')->find($laboratorio);
        $pcs = $em->getRepository('App:Pc')->findBy(
            array('laboratorio'=>$laboratorio,'estado'=>0));
        foreach ($pcs as $pc) {
            $reservacion = $em->getRepository('App:ReservacionPc')->findOneBy(array(
                'fecha' => $fecha,
                'pc' => $pc
            ));
            if (!$reservacion || $reservacion_id==$reservacion->getId())
                return $pc;
        }
        return null;
    }


}
