<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */

class AddDestinatarioFieldSubscriber  implements EventSubscriberInterface{

    private $factory;
    private $em;

    /**
     * AddTarjetaFieldSubscriber constructor.
     */
    public function __construct(FormFactoryInterface $factory, ObjectManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public static function getSubscribedEvents() {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',

        );
    }

    /**
     * Cuando el usuario llene los datos del formulario y haga el envío del mismo,
     * este método será ejecutado.
     */
    public function preSubmit(FormEvent $event) {
        $data = $event->getData();
        if(null===$data){
            return;
        }

        $destinatario= is_array($data['iddestinatario']) ? $data['iddestinatario'] : [$data['iddestinatario']];
        $this->addElements($event->getForm(), $destinatario);
    }

    protected function addElements($form, $destinatario) {
        $em=$this->em;
        $usuarios=$this->em->createQuery('SELECT u FROM App:Usuario u WHERE u.id IN (:id)')
                       ->setParameter('id',$destinatario)
                        ->getResult();
       $form->add('iddestinatario',null,array('choices'=>$usuarios,'required'=>true,'label'=>'message_tofield','attr'=>array('placeholder'=>'message_tofield_placeholder',)));

    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('iddestinatario',null,array('choices'=>array(),'required'=>true,'label'=>'message_tofield','attr'=>array('placeholder'=>'message_tofield_placeholder',)));
        }

    }





}
