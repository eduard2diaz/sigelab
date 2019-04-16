<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintanceListener
{
    private $serviceContainer;

    function __construct(ContainerInterface $serviceContainer) {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @return mixed
     */
    public function getServiceContainer() {
        return $this->serviceContainer;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if("true"==$this->getServiceContainer()->getParameter('is_system_maintance')){
            throw new \Exception("Estamos en mantenimiento");
         /*   $content='En mantenimiento';
            if ($this->getServiceContainer()->has('templating')) {
                $content=$this->getServiceContainer()->get('templating')->render('default/maintance.html.twig');
            }else
            if ($this->getServiceContainer()->has('twig')) {
                $content=$this->getServiceContainer()->get('twig')->render('default/maintance.html.twig');
            }
            $event->setResponse(new Response($content));*/
        }
    }

}
