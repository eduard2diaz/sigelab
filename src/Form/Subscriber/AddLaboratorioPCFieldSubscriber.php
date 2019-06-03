<?php

namespace App\Form\Subscriber;

use App\Entity\Laboratorio;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Propiedad;
use App\Entity\Pieza;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddLaboratorioPCFieldSubscriber  implements EventSubscriberInterface{

    private $factory;
    /**
     * AddTarjetaFieldSubscriber constructor.
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
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
        $laboratorio= is_array($data) ? $data['laboratorio'] : $data->getLaboratorio();
        $this->addElements($event->getForm(), $laboratorio);
    }

    protected function addElements($form, $laboratorio) {
        $form->add($this->factory->createNamed('pc',EntityType::class,null,array(
            'auto_initialize'=>false,
            'required'=>true,
            'class'         =>'App:PC',
            'query_builder'=>function(EntityRepository $repository)use($laboratorio){
                $qb=$repository->createQueryBuilder('pc')
                    ->innerJoin('pc.laboratorio','p');
                if($laboratorio instanceof Laboratorio){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$laboratorio);
                }elseif(is_numeric($laboratorio)){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$laboratorio);
                }else{
                    $qb->where('p.id = :id')
                        ->setParameter('id',null);
                }
                return $qb;
            }
        )));
    }

    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

       if(null==$data->getId()){
           $form->add('pc',null,array('required'=>true,'choices'=>array()));
        }else
       {
           $laboratorio= is_array($data) ? $data['laboratorio'] : $data->getLaboratorio();
           $this->addElements($event->getForm(), $laboratorio);
       }

    }





}
