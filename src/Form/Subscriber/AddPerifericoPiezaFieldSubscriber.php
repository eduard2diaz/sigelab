<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Periferico;
use App\Entity\Pieza;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Description of AddCargoFieldSubscriber
 *
 * @author eduardo
 */
class AddPerifericoPiezaFieldSubscriber implements EventSubscriberInterface
{

    private $factory;

    /**
     * AddTarjetaFieldSubscriber constructor.
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',

        );
    }

    /**
     * Cuando el usuario llene los datos del formulario y haga el envío del mismo,
     * este método será ejecutado.
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        if (null === $data) {
            return;
        }

        $pieza = null;
        if (is_array($data)) {
            if (array_key_exists('pieza', $data))
                $pieza = $data['pieza'];
        } else
            $pieza = $data->getPieza()->getId();


        $periferico = is_array($data) ? $data['periferico'] : $data->getPeriferico();
        if (!$pieza) {
            $form->add('pieza');
            $this->addElementsPropiedad($event->getForm(), $periferico);
        } else {
            $this->addElementsPieza($event->getForm(), $periferico);
            $this->addElementsPiezaPropiedad($event->getForm(), $pieza);
        }
    }

    protected function addElementsPieza($form, $periferico)
    {
        $form->add($this->factory->createNamed('pieza', EntityType::class, null, array(
            'required' => false,
            'auto_initialize' => false,
            'class' => 'App:Pieza',
            'query_builder' => function (EntityRepository $repository) use ($periferico) {
                $qb = $repository->createQueryBuilder('pieza')
                    ->innerJoin('pieza.idperiferico', 'p');
                if ($periferico instanceof Periferico) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $periferico);
                } elseif (is_numeric($periferico)) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $periferico);
                } else {
                    $qb->where('p.id = :id')
                        ->setParameter('id', null);
                }
                return $qb;
            },
            'attr' => array('class' => 'form-control input-medium')
        )));
    }

    protected function addElementsPropiedad($form, $periferico)
    {
        $form->add($this->factory->createNamed('propiedad', EntityType::class, null, array(
            'required' => true,
            'auto_initialize' => false,
            'class' => 'App:Propiedad',
            'query_builder' => function (EntityRepository $repository) use ($periferico) {
                $qb = $repository->createQueryBuilder('propiedad')
                    ->innerJoin('propiedad.idperiferico', 'p');
                if ($periferico instanceof Periferico) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $periferico);
                } elseif (is_numeric($periferico)) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $periferico);
                } else {
                    $qb->where('p.id = :id')
                        ->setParameter('id', null);
                }
                return $qb;
            },
            'attr' => array('class' => 'form-control input-medium')
        )));
    }

    protected function addElementsPiezaPropiedad($form, $pieza)
    {
        $form->add($this->factory->createNamed('propiedad', EntityType::class, null, array(
            'required' => true,
            'auto_initialize' => false,
            'class' => 'App:Propiedad',
            'query_builder' => function (EntityRepository $repository) use ($pieza) {
                $qb = $repository->createQueryBuilder('propiedad')
                    ->innerJoin('propiedad.idpieza', 'p');
                if ($pieza instanceof Pieza) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $pieza);
                } elseif (is_numeric($pieza)) {
                    $qb->where('p.id = :id')
                        ->setParameter('id', $pieza);
                } else {
                    $qb->where('p.id = :id')
                        ->setParameter('id', null);
                }
                return $qb;
            },
            'attr' => array('class' => 'form-control input-medium')
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null == $data->getId()) {
            $form->add('pieza', null, array('label'=>'piece','required' => false, 'choices' => array(), 'attr' => array('class' => 'form-control input-medium')));
        } else {
            $periferico = is_array($data) ? $data['periferico'] : $data->getPeriferico();
            $pieza = is_array($data) ? $data['pieza'] : $data->getPieza();

            $this->addElementsPieza($event->getForm(), $periferico);
            if (!$pieza)
                $this->addElementsPropiedad($event->getForm(), $periferico);
             else
                $this->addElementsPiezaPropiedad($event->getForm(), $pieza);

        }

    }


}
