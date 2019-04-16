<?php

namespace App\Controller;

use App\Entity\Pieza;
use App\Form\PropiedadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Periferico;
use App\Entity\Propiedad;

/*
 * @Route("/propiedad")
 */

/**
 * @Route({
 *     "en": "/property",
 *     "es": "/propiedad",
 *     "fr": "/propriete"
 * })
 */
class PropiedadController extends Controller
{

    /**
     * @Route("/", name="propiedad_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $propiedads = $this->getDoctrine()->getRepository(Propiedad::class)->findAll();

        if ($request->isXmlHttpRequest())
            return $this->render('propiedad/_table.html.twig', [
                'propiedades' => $propiedads,
            ]);

        return $this->render('propiedad/index.html.twig', ['propiedades' => $propiedads]);
    }


    /**
     * @Route("/new", name="propiedad_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $propiedad = new Propiedad();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PropiedadType::class, $propiedad, array('action' => $this->generateUrl('propiedad_new')));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em->persist($propiedad);
                $em->flush();
                $message=$this->get('translator')->trans('property_register_successfully');
                return new JsonResponse(array('mensaje' => $message,
                    'nombre' => $propiedad->getNombre(),
                    'id' => $propiedad->getId(),
                ));
            } else {
                $page = $this->renderView('propiedad/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }


        return $this->render('propiedad/_new.html.twig', [
            'propiedad' => $propiedad,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="propiedad_edit",options={"expose"=true}, methods="GET|POST")
     */
    public function edit(Request $request, Propiedad $propiedad): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $form = $this->createForm(PropiedadType::class, $propiedad, array('action' => $this->generateUrl('propiedad_edit', array('id' => $propiedad->getId()))));
        $form->handleRequest($request);

        if ($form->isSubmitted())
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($propiedad);
                $em->flush();
                $message=$this->get('translator')->trans('property_update_successfully');
                return new JsonResponse(array('mensaje' => $message, 'nombre' => $propiedad->getNombre()));
            } else {
                $page = $this->renderView('propiedad/_form.html.twig', array(
                    'form' => $form->createView(),
                    'form_id' => 'propiedad_edit',
                    'action' => 'update_button',
                ));
                return new JsonResponse(array('form' => $page, 'error' => true));
            }

        return $this->render('propiedad/_new.html.twig', [
            'propiedad' => $propiedad,
            'title' => 'property_edit_title',
            'action' => 'update_button',
            'form_id' => 'propiedad_edit',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete",options={"expose"=true}, name="propiedad_delete")
     */
    public function delete(Request $request, Propiedad $propiedad): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($propiedad);
        $em->flush();
        $message=$this->get('translator')->trans('property_delete_successfully');
        return new JsonResponse(array('mensaje' => $message));
    }

    /**
     * @Route("/{pieza}/searchbypieza",options={"expose"=true}, name="propiedad_searchbypieza")
     */
    public function searchbypieza(Request $request, Pieza $pieza): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $array=array();
        foreach ($pieza->getIdpropiedad()->toArray() as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getNombre()];

        return new Response(json_encode($array));
    }

    /**
     * @Route("/{periferico}/searchbyperiferico",options={"expose"=true}, name="propiedad_searchbyperiferico")
     */
    public function searchbyperiferico(Request $request, Periferico $periferico): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $array=array();
        foreach ($periferico->getIdpropiedad()->toArray() as $value)
            $array[]=['id'=>$value->getId(),'nombre'=>$value->getNombre()];

        return new Response(json_encode($array));
    }
}
