<?php

namespace App\Form;

use App\Entity\Pieza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PiezaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('label'=>'name_field','attr'=>array('autocomplete'=>'off','placeholder'=>'piece_name_placeholder','class'=>'form-control input-xlarge')))
            ->add('idpropiedad',null,array('label'=>'properties','required'=>true,
                'attr'=>array('class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pieza::class,
        ]);
    }
}
