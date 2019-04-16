<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Form\MensajeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route({
 *     "en": "/message",
 *     "es": "/mensaje",
 *     "fr": "/messager",
 * })
 */
class MensajeController extends Controller
{
    /**
     * @Route("/", name="mensaje_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $mensaje_inbox=$this->get('translator')->trans('message_inbox_received');
        $mensajes = $this->getDoctrine()
            ->getRepository(Mensaje::class)
            ->findBy(array('bandeja'=>0,'propietario'=>$this->getUser()),array('fecha'=>'DESC'));

        if ($request->isXmlHttpRequest())
            return new JsonResponse(array(
                'messages'=>$this->renderView('mensaje/_table.html.twig', [
                    'mensajes' => $mensajes
                ]),
                'message_inbox'=>$mensaje_inbox
            ));


        return $this->render('mensaje/index.html.twig', ['mensajes' => $mensajes,'message_inbox'=>$mensaje_inbox]);
    }

    /**
     * @Route("/send", name="mensaje_send", methods="GET")
     */
    public function send(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $mensaje_inbox=$this->get('translator')->trans('message_inbox_sended');
        $mensajes = $this->getDoctrine()
            ->getRepository(Mensaje::class)
            ->findBy(array('bandeja'=>1,'remitente'=>$this->getUser()),array('fecha'=>'DESC'));

        return new JsonResponse(array(
            'messages'=>$this->renderView('mensaje/_table.html.twig', [
                'mensajes' => $mensajes
            ]),
            'message_inbox'=>$mensaje_inbox
        ));

    }


    /**
     * @Route("/recent",options={"expose"=true}, name="mensaje_recent",  methods="GET")
     */
    public function recent(Request $request): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $mensajes = $this->getDoctrine()->getManager()
                         ->createQuery('SELECT m FROM App:Mensaje m JOIN m.propietario p WHERE m.isnew= :new AND p.id= :id AND m.bandeja = 0 ORDER By m.fecha DESC')
                         ->setParameters(array('id'=>$this->getUser()->getId(),'new'=>true))
                         ->setMaxResults(5)
                         ->getResult();

        $count=count($mensajes);
        if ($count> 50)
            $count='+50';

        $cadena = $this->get('translator')->transChoice(
            'Tienes 1 mensaje nuevo | Tienes %total% mensajes nuevos',
            $count,
            array('%total%' => $count)
        );

        return new JsonResponse(array(
            'html'=>$this->renderView('mensaje/_notify.html.twig', [
                'mensajes' => $mensajes
            ]),
            'contador'=> $count,
            'messages_header'=>$cadena
        ));

    }

    /**
     * @Route("/new", name="mensaje_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if(!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();
        $mensaje = new Mensaje();
        $form = $this->createForm(MensajeType::class, $mensaje,array('action'=>$this->generateUrl('mensaje_new'),'em'=>$this->getDoctrine()->getManager()));
        $form->handleRequest($request);
        if ($form->isSubmitted())
            if($form->isValid()) {
            $mensaje->setRemitente($this->getUser());
            $mensaje->setPropietario($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($mensaje);
            foreach ($mensaje->getIddestinatario() as $value){
                $clone=clone $mensaje;
                $clone->setPropietario($value);
                $clone->setBandeja(0);
                $em->persist($clone);
            }
            $em->flush();
                $message=$this->get('translator')->trans('message_register_successfully');
                return new JsonResponse(array('mensaje' => $message,
                    'descripcion' => $mensaje->getDescripcion(),
                    'fecha' => $mensaje->getFecha()->format('d-m-Y H:i'),
                    'noleida' => $mensaje->getNoleida(),
                    'id' => $mensaje->getId(),
                ));
        }else {
                $page = $this->renderView('mensaje/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
                return new JsonResponse(array('form' => $page, 'error' => true,));
            }

        return $this->render('mensaje/_new.html.twig', [
            'mensaje' => $mensaje,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mensaje_show", methods="GET")
     */
    public function show(Mensaje $mensaje): Response
    {
        if($mensaje->getIsnew()){
           $em=$this->getDoctrine()->getManager();
           $mensaje->setIsnew(false);
           $em->persist($mensaje);
           $em->flush();
        }
        return $this->render('mensaje/_show.html.twig', ['mensaje' => $mensaje]);
    }


    /**
     * @Route("/{id}/delete", name="mensaje_delete")
     */
    public function delete(Request $request, Mensaje $mensaje): Response
    {
        if (!$request->isXmlHttpRequest() || $mensaje->getPropietario()!=$this->getUser())
            throw $this->createAccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $em->remove($mensaje);
        $em->flush();
        return new JsonResponse(array('mensaje' => $this->get('translator')->trans('message_delete_successfully')));
    }
}
