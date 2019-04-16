<?php

namespace App\Controller;

use App\Entity\Laboratorio;
use App\Entity\Pc;
use App\Form\PcType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
/*
 * @Route("/pc")
 */

/**
 * @Route({
 *     "en": "/pc",
 *     "es": "/computadora",
 *     "fr": "/ordinateur"
 * })
 */
class PcController extends Controller
{
    /**
     * @Route("/{laboratorio}/new", name="pc_new", methods="GET|POST")
     */
    public function new(Request $request, Laboratorio $laboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$laboratorio);
        $pc = new Pc();
        $pc->setLaboratorio($laboratorio);
        $form = $this->createForm(PcType::class, $pc, array('action' => $this->generateUrl('pc_new', array('laboratorio' => $laboratorio->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($pc);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('pc_register_successfully'),
                    'numero' => $pc->getNumero(),
                    'mac' => $pc->getMac(),
                    'estado' => $this->get('translator')->trans($pc->getEstadoToString()),
                    'id' => $pc->getId(),
                ));
            } else {
                $page = $this->renderView('pc/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('pc/_new.html.twig', [
            'pc' => $pc,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="pc_show",options={"expose"=true}, methods="GET")
     */
    public function show(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$pc);
        return $this->render('pc/_show.html.twig', ['pc' => $pc]);
    }

    /**
     * @Route("/{id}/exportar", name="pc_exportar", methods="GET")
     */
    public function exportar(Pc $pc): Response
    {
        $html = $this->renderView('pc/_pdf.html.twig', array(
            'pc'  => $pc
        ));

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'file.pdf'
        );
    }



    /**
     * @Route("/{id}/edit", name="pc_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$pc);
        $form = $this->createForm(PcType::class, $pc, array('es_jefe_tecnico'=>$this->isGranted('ROLE_JEFETECNICO'),'action' => $this->generateUrl('pc_edit', array('id' => $pc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($pc);
                $em->flush();
                return new JsonResponse(array('mensaje' => $this->get('translator')->trans('pc_update_successfully'),
                    'numero' => $pc->getNumero(),
                    'mac' => $pc->getMac(),
                    'estado' => $this->get('translator')->trans($pc->getEstadoToString()),
                    'html'=>$this->renderView('pc/_info.html.twig',['pc'=>$pc])
                ));
            } else {
                $page = $this->renderView('pc/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'pc_edit',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('pc/_new.html.twig', [
            'pc' => $pc,
            'action' => 'update_button',
            'form_id' => 'pc_edit',
            'title'=>'pc_edit_title',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="pc_delete",options={"expose"=true})
     */
    public function delete(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$pc);
        $em = $this->getDoctrine()->getManager();
        $em->remove($pc);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('pc_delete_successfully')));
    }
}
