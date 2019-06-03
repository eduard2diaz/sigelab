<?php

namespace App\EventSubscriber;

use App\Entity\Pc;
use App\Services\NotificacionService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PcSubscriber implements EventSubscriber
{
    private $serviceContainer;

    function __construct(ContainerInterface $serviceContainer) {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return ContainerInterface
     */
    public function getServiceContainer(): ContainerInterface
    {
        return $this->serviceContainer;
    }



    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Pc) {
            $em=$this->getServiceContainer()->get('doctrine')->getManager();
            $consulta=$em->createQuery('Select u from App:Usuario u join u.idrol r WHERE r.nombre= :nombre');
            $consulta->setParameter('nombre','ROLE_JEFETECNICOINSTITUCIONAL');
            $consulta->setMaxResults(1);
            $result=$consulta->getResult();
            if(!empty($result)){
                $usuario=$result[0];
                $message='La computadora con mac '.$entity->getMac().' ha sido modificada';
                $this->getServiceContainer()->get('app.notificacion_service')->nuevaNotificacion($usuario->getId(),$message);
            }
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'postUpdate',
        ];
    }
}
