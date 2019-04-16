<?php

namespace App\Form;

use App\Entity\Tiporeparacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TiporeparacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('label'=>'name_field','attr'=>array('autocomplete'=>'off','placeholder'=>'typeofrepair_name_placeholder','class'=>'form-control input-xlarge')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tiporeparacion::class,
        ]);
    }
}
