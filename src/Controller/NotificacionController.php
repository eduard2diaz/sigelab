<?php

namespace App\Controller;

use App\Form\NotificacionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notificacion;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route({
 *     "en": "/notification",
 *     "es": "/notificacion",
 *     "fr": "/notificationr",
 * })
 */
class NotificacionController extends AbstractController
{

    /**
     * @Route("/", name="notificacion_index", methods="GET",options={"expose"=true})
     */
    public function index(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->query->get('_format') == 'json') {

                if (null == $this->getUser()->getLastlogout()) {
                    $notificacions = $this->getDoctrine()->getRepository(Notificacion::class)->findBy(['destinatario' => $this->getUser()->getId(),'leida'=>false], ['fecha' => 'DESC'], 5);
                } else {
                    $consulta = $this->getDoctrine()->getManager()->createQuery('SELECT n FROM App:Notificacion n JOIN n.destinatario u WHERE u.id= :usuario AND n.fecha>= :fecha  AND n.leida= FALSE');
                    $consulta->setParameters(['usuario' => $this->getUser()->getId(), 'fecha' => $this->getUser()->getLastlogout()]);
                    $consulta->setMaxResults(5);
                    $notificacions = $consulta->getResult();
                }

                return $this->json([
                    'contador' => count($notificacions),
                    'html' => $this->renderView('notificacion/ajax/_json.html.twig', ['notificaciones' => $notificacions])
                ]);
            }

            $notificacions = $this->getDoctrine()->getRepository(Notificacion::class)->findBy(['destinatario' => $this->getUser()], ['fecha' => 'DESC']);
            return $this->render('notificacion/_table.html.twig', [
                'notificacions' => $notificacions,
            ]);

        }

        $notificacions = $this->getDoctrine()->getRepository(Notificacion::class)->findBy(['destinatario' => $this->getUser()], ['fecha' => 'DESC']);
        return $this->render('notificacion/index.html.twig', [
            'notificacions' => $notificacions,
        ]);
    }

    /**
     * @Route("/{id}/show", name="notificacion_show", methods="GET")
     */
    public function show(Request $request, Notificacion $notificacion): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('VIEW',$notificacion);

        if(!$notificacion->getLeida()){
            $em=$this->getDoctrine()->getManager();
            $notificacion->setLeida(true);
            $em->persist($notificacion);
            $em->flush();
        }
        return $this->render('notificacion/_show.html.twig', [
            'notificacion' => $notificacion,
        ]);
    }

    /**
     * @Route("/{id}/delete",name="notificacion_delete")
     */
    public function delete(Request $request, Notificacion $notificacion, TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
            throw $this->createAccessDeniedException();

        $this->denyAccessUnlessGranted('DELETE',$notificacion);
        $em = $this->getDoctrine()->getManager();
        $em->remove($notificacion);
        $em->flush();
        return $this->json(array('mensaje' => $translator->trans('notificacion_delete_successfully')));
    }

}
