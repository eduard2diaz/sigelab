<?php

namespace App\Form;

use App\Entity\TiempoMaquina;
use App\Form\Subscriber\AddLaboratorioPCFieldSubscriber;
use App\Form\Transformer\DateTimetoStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TiempoMaquinaType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $laboratorios=$this->token->getToken()->getUser()->getFacultad()->getIdlaboratorio()->toArray();

        $builder
            ->add('fechaInicio',TextType::class,['label'=>'startdate_field','attr'=>['class'=>'form-control']])
            ->add('fechaFin',TextType::class,['required'=>false,'label'=>'enddate_field','attr'=>['class'=>'form-control']])
            ->add('usuario',null,['label'=>'user','required'=>true,'attr'=>['class'=>'form-control']])
            ->add('laboratorio', null, array(
                'label'=>'laboratory',
                'required' => false,
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
        ;

        $builder->get('fechaInicio')->addModelTransformer(new DateTimetoStringTransformer());
        $builder->get('fechaFin')->addModelTransformer(new DateTimetoStringTransformer());

        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddLaboratorioPCFieldSubscriber($factory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TiempoMaquina::class,
        ]);
    }
}
