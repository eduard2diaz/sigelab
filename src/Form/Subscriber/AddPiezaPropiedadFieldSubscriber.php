<?php

namespace App\Form\Subscriber;

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
class AddPiezaPropiedadFieldSubscriber  implements EventSubscriberInterface{

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
        $pieza= is_array($data) ? $data['pieza'] : $data->getPieza();
        $this->addElements($event->getForm(), $pieza);
    }

    protected function addElements($form, $pieza) {
        $form->add($this->factory->createNamed('propiedad',EntityType::class,null,array(
            'auto_initialize'=>false,
            'class'         =>'App:Propiedad',
            'query_builder'=>function(EntityRepository $repository)use($pieza){
                $qb=$repository->createQueryBuilder('propiedad')
                    ->innerJoin('propiedad.idpieza','p');
                if($pieza instanceof Pieza){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$pieza);
                }elseif(is_numeric($pieza)){
                    $qb->where('p.id = :id')
                        ->setParameter('id',$pieza);
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
           $form->add('propiedad',null,array('choices'=>array()));
        }else
       {
           $pieza= is_array($data) ? $data['pieza'] : $data->getPieza();
           $this->addElements($event->getForm(), $pieza);
       }

    }





}
