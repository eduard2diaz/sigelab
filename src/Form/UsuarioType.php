<?php

namespace App\Form;

use App\Entity\Rol;
use App\Entity\Usuario;
use App\Entity\Cargo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\Subscriber\AddCargoFieldSubscriber;
use Symfony\Component\Validator\Constraints as Assert;


class UsuarioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $esAdmin = $options['esAdmin'];
        $disabled = $options['disab'];
        $auxdisabled = $options['disab'];
        if ($esAdmin)
            $auxdisabled = false;

        $builder
            ->add('nombre', TextType::class, array('label'=>'name_field','attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large')))
            ->add('apellido', TextType::class, array('label' => 'surname_field', 'attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large')))
            ->add('usuario', TextType::class, array('label'=>'username_field','disabled' => $disabled,'attr' => array('autocomplete' => 'off', 'class' => 'form-control input-medium')))
            ->add('correo', EmailType::class, array('label'=>'email_field','disabled' => $disabled,
                'help' => 'email_field_help',
                'attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large')))
            ->add('activo', null, array('label'=>'active_field','disabled' => $disabled, 'required' => false, 'attr' => array('data-on-text' => 'active_yes', 'data-off-text' => 'active_no','data-size'=>'small')))
            ->add('facultad', null, array('label'=>'faculty','disabled' => $disabled, 'placeholder' => 'faculty_field_select', 'required' => true, 'attr' => array('class' => 'form-control input-medium')))
            ->add('file', FileType::class, array('required' => false,
                'attr' => array('style' => 'display:none',
                    'accept' => 'image/*',/* 'accept' => '.jpg,.jpeg,.png,.gif,.bmp,.tiff'*/)
            ));


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $obj) {
            $form = $obj->getForm();
            $data = $obj->getData();
            $required = false;
            $constraint=array();
            if (null == $data->getId()){
                $required = true;
                $constraint[]=new Assert\NotBlank();
            }

            $form->add('password', RepeatedType::class, array('required' => $required,
                'type' => PasswordType::class,
                'constraints' => $constraint,
                'invalid_message' => 'confirm_password_field_error',
                'first_options' => array('label' => 'password_field'
                , 'attr' => array('class' => 'form-control input-medium')),
                'second_options' => array('label' => 'confirm_password_field', 'attr' => array('class' => 'form-control input-medium'))
            ));
        });

       // if ($esAdmin)
            $builder->add('idrol', null, array('label'=>'role_field','disabled' => $disabled, 'required' => true, 'attr' => array('class' => 'form-control input-medium')));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
        $resolver->setRequired(['esAdmin', 'disab']);
    }
}
