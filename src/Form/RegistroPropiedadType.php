<?php

namespace App\Form;

use App\Entity\RegistroPropiedad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistroPropiedadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        //    ->add('fecha',TextType::class,array('attr'=>array('class'=>'form-control input-medium')))
            ->add('valor',TextType::class,array('label'=>'value_field','attr'=>array('autocomplete'=>'off','class'=>'form-control input-medium')))
            ->add('propiedad',null,array('label'=>'property','required'=>true,'attr'=>array('class'=>'form-control input-medium')))
           // ->add('pc')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegistroPropiedad::class,
        ]);
    }
}
