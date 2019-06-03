<?php

namespace App\Form;

use App\Entity\RegistroPeriferico;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Subscriber\AddPiezaPropiedadFieldSubscriber;
use App\Form\Subscriber\AddPerifericoPiezaFieldSubscriber;

class RegistroPerifericoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valor',TextType::class,array('label'=>'value_field','attr'=>array('autocomplete'=>'off','class'=>'form-control input-medium')))
            ->add('propiedad',null,array('label'=>'property','choices'=>array(),'required'=>true,'attr'=>array('class'=>'form-control input-medium')))
            ->add('pieza',null,array('label'=>'piece','choices'=>array(),'placeholder'=>'pieceregister_form_piece_placeholder','required'=>false,'attr'=>array('class'=>'form-control input-medium')))
            ->add('periferico',null,array('label'=>'peripheral','placeholder'=>'peripheralregister_form_peripheral_placeholder','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
        ;

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddPerifericoPiezaFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegistroPeriferico::class,
        ]);
    }
}
