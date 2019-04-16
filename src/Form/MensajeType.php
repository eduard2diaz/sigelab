<?php

namespace App\Form;

use App\Entity\Mensaje;
use App\Form\Subscriber\AddDestinatarioFieldSubscriber;
use App\Form\Transformer\DestinatarioTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MensajeType extends AbstractType
{
    private $em;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em=$options['em'];
        $builder
        ->add('iddestinatario',null,array('choices'=>array(),'required'=>true,'label'=>'message_tofield','attr'=>array('placeholder'=>'message_tofield_placeholder',)))
        ->add('descripcion',TextareaType::class,array('required'=>true,'label'=>'message','attr'=>array('rows'=>5,'autocomplete'=>'off','placeholder'=>'message_messagefield_placeholder','class'=>'form-control input-xxlarge')))
        ;
        $factory=$builder->getFormFactory();
        $builder->addEventSubscriber(new AddDestinatarioFieldSubscriber($factory,$this->em));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mensaje::class,
        ]);
        $resolver->setRequired(['em']);
    }
}
