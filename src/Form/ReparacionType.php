<?php

namespace App\Form;

use App\Entity\Reparacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\Transformer\DatetoStringTransformer;

class ReparacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', TextType::class, array('label'=>'date_field','attr' => array(
                'data-date-format'=>"yyyy-mm-dd",
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('descripcion', TextareaType::class, array('label'=>'description_field', 'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-xxlarge'
            )))
            ->add('precio', NumberType::class, array('label'=>'price_field','attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('tiporeparacion', null, array('label'=>'typeofrepair','required'=>true,'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )));

        $builder->get('fecha')
            ->addModelTransformer(new DatetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reparacion::class,
        ]);
    }
}
