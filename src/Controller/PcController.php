<?php

namespace App\Controller;

use App\Entity\Laboratorio;
use App\Entity\Pc;
use App\Form\PcType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Snappy\Pdf;

/**
 * @Route({
 *     "en": "/pc",
 *     "es": "/computadora",
 *     "fr": "/ordinateur"
 * })
 */
class PcController extends AbstractController
{
    /**
     * @Route("/{laboratorio}/new", name="pc_new", methods="GET|POST")
     */
    public function new(Request $request, Laboratorio $laboratorio, TranslatorInterface $translator): Response
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
                return $this->json(array('mensaje' => $translator->trans('pc_register_successfully'),
                    'numero' => $pc->getNumero(),
                    'mac' => $pc->getMac(),
                    'estado' => $translator->trans($pc->getEstadoToString()),
                    'id' => $pc->getId(),
                ));
            } else {
                $page = $this->renderView('pc/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return $this->json(array('form' => $page, 'error' => true,));
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
     * @Route("/{id}/ajaxdetails", name="pc_ajaxdetails",options={"expose"=true}, methods="GET")
     */
    public function ajaxdetails(Request $request, Pc $pc): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        return $this->render('pc/_details.html.twig', ['pc' => $pc]);
    }

    /**
     * @Route("/{id}/exportar", name="pc_exportar", methods="GET")
     */
    public function exportar(Pc $pc,Pdf $pdf): Response
    {
        $html = $this->renderView('pc/_pdf.html.twig', array(
            'pc'  => $pc
        ));

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }

    /**
     * @Route("/{id}/edit", name="pc_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Pc $pc, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('EDIT',$pc);
        $laboratorioOriginal=$pc->getLaboratorio()->getId();
        $form = $this->createForm(PcType::class, $pc, array('es_jefe_tecnico'=>$this->isGranted('ROLE_JEFETECNICO'),'action' => $this->generateUrl('pc_edit', array('id' => $pc->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($pc);
                $em->flush();
                return $this->json(array('mensaje' => $translator->trans('pc_update_successfully'),
                    'numero' => $pc->getNumero(),
                    'mac' => $pc->getMac(),
                    'estado' => $translator->trans($pc->getEstadoToString()),
                    'html'=>$this->renderView('pc/_info.html.twig',['pc'=>$pc]),
                    'cambiolaboratorio'=>$pc->getLaboratorio()->getId()!=$laboratorioOriginal
                ));
            } else {
                $page = $this->renderView('pc/_form.html.twig', array(
                    'form' => $form->createView(),
                    'action' => 'update_button',
                    'form_id' => 'pc_edit',
                ));
                return $this->json(array('form' => $page, 'error' => true,));
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
    public function delete(Request $request, Pc $pc, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$pc);
        $em = $this->getDoctrine()->getManager();
        $em->remove($pc);
        $em->flush();
        return $this->json(array('mensaje' => $translator->trans('pc_delete_successfully')));
    }

    /**
     * @Route("/{laboratorio}/findbylaboratorio", name="pc_findbylaboratorio",options={"expose"=true})
     */
    public function findbylaboratorio(Request $request, Laboratorio $laboratorio): Response
    {
        if (!$request->isXmlHttpRequest())
           throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $consulta=$em->createQuery('SELECT p.id, p.numero FROM App:Pc p JOIN p.laboratorio l WHERE l.id= :id AND p.estado=0');
        $consulta->setParameter('id',$laboratorio->getId());
        $result=$consulta->getResult();
        return $this->json($result);
    }

    /**
     * @Route("/search", name="pc_search",options={"expose"=true})
     */
    public function search(Request $request/*,PaginatorInterface $paginator*/)
    {
        $query = $request->get('query');
        if(!$query || $query=='')
            throw new \LogicException('Falta el parámetro query');

        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $content = '';
            $consulta = $em->createQuery('SELECT p.id, p.numero, p.mac FROM App:Pc p WHERE p.mac like :parametro');
            $consulta->setParameter('parametro', '%' . $query . '%');
            $consulta->setMaxResults(2);
            //Obtengo el listado de pc
            $pcs = $consulta->getResult();
            $content = $this->renderView('pc/ajax/search_quickresult.html.twig', ['query'=>$query,'pcs' => $pcs]);
            return new Response($content);
        }

        return new Response(1);
        $consulta = $em->createQuery('SELECT u.id, u.nombre,u.rutaFoto,u.ultimoLogin, u.ultimoLogout, i.nombre as institucion,p.nombre as pais, 1 as esAutor FROM App:Autor u JOIN u.institucion i join i.pais p WHERE u.nombre like :parametro');
        $consulta->setParameter('parametro', '%' . $query . '%');
        $usuarios = $consulta->getResult();

        $consulta = $em->createQuery('SELECT p.id, p.titulo, a.nombre as autor, 0 as esAutor FROM App:Publicacion p JOIN p.autor a WHERE p.estado=1 AND p.titulo like :parametro');
        $consulta->setParameter('parametro', '%' . $query . '%');
        $publicaciones = $consulta->getResult();

        $pagination = $paginator->paginate(
        //$consulta,
            array_merge($usuarios,$publicaciones), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('publicacion/search_result.html.twig', array('pagination' => $pagination));


    }
}
