<?php

namespace App\Form;

use App\Entity\ReservacionLaboratorio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReservacionLaboratorioType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id=$options['data']->getId();
        $builder
            ->add('fechainicio', TextType::class, array('label' => 'startdate_field', 'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )))
            ->add('fechafin', TextType::class, array('label' => 'enddate_field', 'attr' => array(
                'autocomplete' => 'off',
                'class' => 'form-control input-medium'
            )));

        if(null==$id){
            $laboratorios=$this->token->getToken()->getUser()->getFacultad()->getIdlaboratorio()->toArray();
            $builder->add('laboratorio', null, array(
                'label'=>'laboratory',
                'required' => true,
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
        }else{
            if(in_array('ROLE_JEFETECNICOINSTITUCIONAL',$this->token->getToken()->getUser()->getRoles()))
                $builder->add('laboratorio', null, array(
                    'label'=>'laboratory',
                    'required' => true));
            else{
                $laboratorios=$this->token->getToken()->getUser()->getFacultad()->getIdlaboratorio()->toArray();
                $builder->add('laboratorio', null, array(
                    'label'=>'laboratory',
                    'required' => true,
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


        }



        $builder->get('fechainicio')
            ->addModelTransformer(new DateTimetoStringTransformer());
        $builder->get('fechafin')
            ->addModelTransformer(new DateTimetoStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReservacionLaboratorio::class,
        ]);
    }
}
