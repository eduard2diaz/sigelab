<?php

namespace App\Form;

use App\Entity\Laboratorio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class LaboratorioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $esJefeInstitucional=$options['esJefeInstitucional'];
        $builder
            ->add('nombre',TextType::class,array('required'=>true,'label'=>'name_field','attr'=>array('autocomplete'=>'off','placeholder'=>'laboratory_name_placeholder','class'=>'form-control input-xlarge')))
            ->add('enfuncionamiento',null,array('label'=>'functioning_field'))
            ->add('facultad',null,array('required'=>true,'label'=>'faculty','disabled'=>!$esJefeInstitucional,'attr'=>array('class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Laboratorio::class,
            'esJefeInstitucional'=>true
        ]);
    }
}
