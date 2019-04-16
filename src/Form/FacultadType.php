<?php

namespace App\Form;

use App\Entity\Facultad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class FacultadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',TextType::class,array('label'=>'name_field','attr'=>array('autocomplete'=>'off','placeholder'=>'faculty_name_placeholder','class'=>'form-control input-xlarge')))
            ->add('idlaboratorio',null,array('label'=>'laboratory_access_field',
               /* 'query_builder'=>function(EntityRepository $repository){
                    $qb=$repository->createQueryBuilder('laboratorio')
                        //Como vez en las relaciones MM tambein se pueden hace join
                        ->where('laboratorio.enfuncionamiento= :value')
                        ->setParameter('value',true);
                    return $qb;
                } ,*/

            //   'label'=>'Laboratorios con acceso',
                'choice_label' => function($category, $key, $value) {
                    return $category->getNombre();
                },
                'group_by' => function($category, $key, $value) {
                    // agrupando los laboratorios por la facultad
                    return $category->getFacultad();
                },
                'attr'=>array('class'=>'form-control input-medium')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facultad::class,
        ]);
    }
}
