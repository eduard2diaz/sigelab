<?php

namespace App\Controller;

use App\Entity\TiempoMaquina;
use App\Form\TiempoMaquinaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route({
 *     "en": "/machinetime",
 *     "es": "/tiempomaquina",
 *     "fr": "/tempsmachine",
 * })
 */
class TiempoMaquinaController extends AbstractController
{
    /**
     * @Route("/", name="tiempo_maquina_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->isGranted('ROLE_JEFETECNICOINSTITUCIONAL'))
            $tiempoMaquinas = $em->getRepository(TiempoMaquina::class)->findAll();
        else {
            $consulta = $em->createQuery('SELECT tm from App:TiempoMaquina tm JOIN tm.laboratorio l JOIN l.facultad f WHERE f.id= :facultad');
            $consulta->setParameter('facultad', $this->getUser()->getFacultad()->getId());
            $tiempoMaquinas = $consulta->getResult();
        }

        if ($request->isXmlHttpRequest())
            return $this->render('tiempo_maquina/_table.html.twig', [
                'tiempo_maquinas' => $tiempoMaquinas,
            ]);

        return $this->render('tiempo_maquina/index.html.twig', [
            'tiempo_maquinas' => $tiempoMaquinas,
        ]);
    }

    /**
     * @Route("/new", name="tiempo_maquina_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $tiempo_maquina = new TiempoMaquina();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TiempoMaquinaType::class, $tiempo_maquina, array('action' => $this->generateUrl('tiempo_maquina_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($tiempo_maquina);
                $em->flush();
                $message = $translator->trans('machine_time_register_successfully');

                return $this->json(array('mensaje' => $message,
                    'fechainicio' => $tiempo_maquina->getFechaInicio()->format('d-m-Y H:i'),
                    'pc' => $tiempo_maquina->getPc()->getNumero() . '/' . $tiempo_maquina->getLaboratorio()->getNombre(),
                    'nombre' => $tiempo_maquina->getUsuario()->getNombre(),
                    'id' => $tiempo_maquina->getId(),
                ));
            } else {
                $page = $this->renderView('tiempo_maquina/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return $this->json(array('form' => $page, 'error' => true,));
            }


        return $this->render('tiempo_maquina/_new.html.twig', [
            'tiempo_maquina' => $tiempo_maquina,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tiempo_maquina_show",options={"expose"=true}, methods={"GET"})
     */
    public function show(Request $request, TiempoMaquina $tiempoMaquina): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('tiempo_maquina/_show.html.twig', [
            'tiempo_maquina' => $tiempoMaquina,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tiempo_maquina_edit",options={"expose"=true},  methods={"GET","POST"})
     */
    public function edit(Request $request, TiempoMaquina $tiempoMaquina, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $form = $this->createForm(TiempoMaquinaType::class, $tiempoMaquina, array('action' => $this->generateUrl('tiempo_maquina_edit', array('id' => $tiempoMaquina->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($tiempoMaquina);
                $em->flush();
                $message = $translator->trans('machine_time_update_successfully');
                return $this->json(array('mensaje' => $message,
                    'fechainicio' => $tiempoMaquina->getFechaInicio()->format('d-m-Y H:i'),
                    'pc' => $tiempoMaquina->getPc()->getNumero() . '/' . $tiempoMaquina->getLaboratorio()->getNombre(),
                    'nombre' => $tiempoMaquina->getUsuario()->getNombre(),
                ));
            } else {
                $page = $this->renderView('tiempo_maquina/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'tiempo_maquina_edit',
                    'action' => 'update_button',
                ));
                return $this->json(array('form' => $page, 'error' => true));
            }

        return $this->render('tiempo_maquina/_new.html.twig', [
            'tiempo_maquina' => $tiempoMaquina,
            'title' => 'machine_time_edit_title',
            'action' => 'update_button',
            'form_id' => 'tiempo_maquina_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tiempo_maquina_delete", options={"expose"=true})
     */
    public function delete(Request $request, TiempoMaquina $tiempoMaquina, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tiempoMaquina);
        $entityManager->flush();
        return $this->json(array('mensaje' => $translator->trans('machine_time_delete_successfully')));
    }
}
