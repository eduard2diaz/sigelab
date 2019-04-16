<?php

namespace App\Form;

use App\Entity\Pc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;


class PcType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('numero', TextType::class, array('label'=>'pc_name','attr' => array('autocomplete'=>'off','placeholder' => 'pc_name_placeholder', 'class' => 'form-control input-medium')))
            ->add('mac', TextType::class, array('label'=>'mac_field','attr' => array('autocomplete'=>'off','class' => 'form-control input-medium')))
            ->add('estado', ChoiceType::class, array('label'=>'state_field','choices' => [
                'pc_state_exploitation' => 0,
                'pc_state_pendingmaintenance' => 1,
                'pc_state_pendingcancel' => 2,
            ], 'attr' => array('autocomplete'=>'off','class' => 'form-control input-medium')));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $obj) {
            $data = $obj->getData();
            $form = $obj->getForm();


            $es_jefe_tecnico=$form->getConfig()->getOption('es_jefe_tecnico');
            if (null != $data->getId() && true==$es_jefe_tecnico)
                $form->add('laboratorio',null,array(
                    'required'=>true,
                         'query_builder'=>function(EntityRepository $repository){
                             $qb=$repository->createQueryBuilder('laboratorio')
                                 //Como vez en las relaciones MM tambein se pueden hace join
                                 ->where('laboratorio.enfuncionamiento= :value')
                                 ->setParameter('value',true);
                             return $qb;
                         } ,
                        'label'=>'laboratory',
                        'choice_label' => function($category, $key, $value) {
                            return $category->getNombre();
                        },
                        'group_by' => function($category, $key, $value) {
                            // agrupando los laboratorios por la facultad
                            return $category->getFacultad();
                        },
                        'attr'=>array('class'=>'form-control input-medium')));
});
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pc::class,
            'es_jefe_tecnico'=>false
        ]);
    }
}
