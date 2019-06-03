<?php

namespace App\Form;

use App\Entity\Laboratorio;
use App\Entity\ReservacionPc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ReservacionPcType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $laboratorios=$options['laboratorios'];
        $builder
            ->add('laboratorio', EntityType::class, array(
                'label'=>'laboratory',
               // 'mapped'=>false,
                'required' => true,
                'class'=>Laboratorio::class,
                'placeholder'=>'reservationlaboratorio_error_laboratorio_blank',
                'preferred_choices'=>array(),
                'query_builder'=>function(EntityRepository $repository) use($laboratorios){
                    $qb=$repository->createQueryBuilder('laboratorio')
                        //Como vez en las relaciones MM tambein se pueden hace join
                        ->where('laboratorio.enfuncionamiento= :value AND laboratorio.id IN (:laboratorio)')
                        ->setParameters(array('value'=>true,'laboratorio'=>$laboratorios))
                    ;
                    return $qb;
                } ,

                'choice_label' => function ($category, $key, $value) {
                    return $category->getNombre();
                },
                'group_by' => function ($category, $key, $value) {
                    // agrupando los laboratorios por la facultad
                    return $category->getFacultad();
                },
                'attr' => array('class' => 'form-control input-medium')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReservacionPc::class,
        ]);
        $resolver->setRequired('laboratorios');
    }
}
