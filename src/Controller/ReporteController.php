<?php

namespace App\Controller;

use App\Entity\Laboratorio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/reporte")
 */
class ReporteController extends AbstractController
{
    /**
     * @Route("/reservacionrfacultad", name="reporte_reservacionfacultad",options={"expose"=true})
     * Cantidad de reservaciones por facultad en un periodo determinado
     */
    public function reservacionFacultad(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = new \DateTime($request->request->get('finicio'));
        $fechaFin = new \DateTime($request->request->get('ffin'));

        $consulta = $em->createQuery('SELECT COUNT(r.id) as cantidad, f.nombre as facultad FROM App:ReservacionPc r JOIN r.usuario u JOIN u.facultad f WHERE r.fecha>= :finicio AND r.fecha<= :ffin GROUP By f.nombre');
        $consulta->setParameters(['finicio' => $fechaInicio, 'ffin' => $fechaFin]);
        $reservaciones = $consulta->getResult();

        $parameters = [
            'view' => $this->renderView('reporte/reservacionfacultad.html.twig', ['title' => 'report_reservationbyfaculty']),
            'data' => json_encode($reservaciones)
        ];

        if (count($reservaciones) == 0)
            $parameters['error_message'] = $translator->trans('report_nodatafound');

        return $this->json($parameters);
    }

    /**
     * @Route("/reservacionlaboratorio", name="reporte_reservacionlaboratorio",options={"expose"=true})
     * Cantidad de reservaciones por facultad en un periodo determinado para una determinado laboratorio
     */
    public function reservacionLaboratorio(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = new \DateTime($request->request->get('finicio'));
        $fechaFin = new \DateTime($request->request->get('ffin'));

        $consulta = $em->createQuery('SELECT COUNT(r.id) as cantidad, l.nombre as laboratorio FROM App:ReservacionPc r JOIN r.pc p JOIN p.laboratorio l join l.facultad f WHERE r.fecha>= :finicio AND r.fecha<= :ffin GROUP By l.nombre');
        $consulta->setParameters(['finicio' => $fechaInicio, 'ffin' => $fechaFin]);
        $reservaciones = $consulta->getResult();

        $parameters = [
            'view' => $this->renderView('reporte/reservacionfacultad.html.twig', ['title' => 'report_reservationbylaboratory']),
            'data' => json_encode($reservaciones)
        ];

        if (count($reservaciones) == 0)
            $parameters['error_message'] = $translator->trans('report_nodatafound');

        return $this->json($parameters);
    }

    /**
     * @Route("/tiempomaquinafacultad", name="reporte_tiempomaquinafacultad",options={"expose"=true})
     * Cantidad de tiempos de maquina por facultad en un periodo determinado
     */
    public function tiempoMaquinaFacultad(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = new \DateTime($request->request->get('finicio'));
        $fechaFin = new \DateTime($request->request->get('ffin'));

        $consulta = $em->createQuery('SELECT COUNT(tm.id) as cantidad, f.nombre as facultad FROM App:TiempoMaquina tm JOIN tm.usuario u JOIN u.facultad f WHERE tm.fechaInicio>= :finicio AND tm.fechaFin<= :ffin GROUP By f.nombre');
        $consulta->setParameters(['finicio' => $fechaInicio, 'ffin' => $fechaFin]);
        $reservaciones = $consulta->getResult();

        $parameters = [
            'view' => $this->renderView('reporte/reservacionfacultad.html.twig', ['title' => 'report_machinetimebyfaculty']),
            'data' => json_encode($reservaciones)
        ];

        if (count($reservaciones) == 0)
            $parameters['error_message'] = $translator->trans('report_nodatafound');

        return $this->json($parameters);
    }

    /**
     * @Route("/tiempomaquinalaboratorio", name="reporte_tiempomaquinalaboratorio",options={"expose"=true})
     * Cantidad de reservaciones por facultad en un periodo determinado para una determinado laboratorio
     */
    public function tiempoMaquinaLaboratorio(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaInicio = new \DateTime($request->request->get('finicio'));
        $fechaFin = new \DateTime($request->request->get('ffin'));
        $consulta = $em->createQuery('SELECT COUNT(tm.id) as cantidad, l.nombre as laboratorio FROM App:TiempoMaquina tm JOIN tm.pc p JOIN p.laboratorio l join l.facultad f WHERE tm.fechaInicio>= :finicio AND tm.fechaFin<= :ffin GROUP By l.nombre');
        $consulta->setParameters(['finicio' => $fechaInicio, 'ffin' => $fechaFin]);
        $reservaciones = $consulta->getResult();

        $parameters=[
            'view' => $this->renderView('reporte/reservacionfacultad.html.twig', ['title' => 'report_machinetimebylaboratory']),
            'data' => json_encode($reservaciones)
        ];

        if (count($reservaciones) == 0)
            $parameters['error_message'] = $translator->trans('report_nodatafound');

        return $this->json($parameters);
    }


}
