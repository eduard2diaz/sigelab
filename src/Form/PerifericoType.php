<?php

namespace App\Form;

use App\Entity\Periferico;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PerifericoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('label'=>'name_field','attr'=>array('autocomplete'=>'off','placeholder'=>'peripheral_name_placeholder','class'=>'form-control input-xlarge')))
            ->add('tipo',ChoiceType::class,array('label'=>'type_field',
                'choices'=>array(
                  'peripheral_type_in'=>'in',
                  'peripheral_type_out'=>'out',
                  'peripheral_type_inout'=>'inout',
                  'peripheral_type_storage'=>'storage',
                ),
                'attr'=>array('class'=>'form-control input-medium')))

            ->add('idpropiedad',null,array('required'=>true,'label'=>'properties',
                'attr'=>array('class'=>'form-control input-medium')))

            ->add('idpieza',null,array('label'=>'pieces',
                'attr'=>array('class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Periferico::class,
        ]);
    }
}
