<?php

namespace App\Form;

use App\Entity\RegistroPieza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Subscriber\AddPiezaPropiedadFieldSubscriber;

class RegistroPiezaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valor',TextType::class,array('label'=>'value_field','attr'=>array('autocomplete'=>'off','class'=>'form-control input-medium')))
            ->add('pieza',null,array('label'=>'piece','placeholder'=>'pieceregister_form_piece_placeholder','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddPiezaPropiedadFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegistroPieza::class,
        ]);
    }
}
