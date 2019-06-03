<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Debug\Debug;

class TiempoMaquinaValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\TiempoMaquina */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof TiempoMaquina) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\UniqueEntity');
        }

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $constraint->em));
            }
        } else {
            $em = $this->registry->getManagerForClass(get_class($value));

            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', get_class($value)));
            }
        }

        $class = $em->getClassMetadata(get_class($value));
        $repository = $em->getRepository(get_class($value));


        if (!is_string($constraint->from)) {
            throw new UnexpectedTypeException($constraint->from, 'string');
        } else
            if (!$class->hasField($constraint->from) && !$class->hasAssociation($constraint->from))
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->from));


        $fechaInicio = $pa->getValue($value, $constraint->from);

        if (!is_string($constraint->to)) {
            throw new UnexpectedTypeException($constraint->to, 'string');
        } else
            if (!$class->hasField($constraint->to) && !$class->hasAssociation($constraint->to))
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->to));

        $fechaFin = $pa->getValue($value, $constraint->to);
        $id = $pa->getValue($value, 'id');
        $usuario = $pa->getValue($value, 'usuario')->getId();
        $pc = $pa->getValue($value, 'pc')->getId();
        $parameters = [
            'fechainicio' => $fechaInicio,
            'usuario' => $usuario,
            'pc' => $pc,
        ];
        if (null == $fechaFin) {
            if (!$id) {
                $cadena = "SELECT COUNT(tm) FROM App:TiempoMaquina tm JOIN tm.usuario u JOIN tm.pc p WHERE (u.id= :usuario OR p.id= :pc) AND :fechainicio >= tm.fechaInicio AND :fechainicio <= tm.fechaFin";
            } else {
                $cadena = "SELECT COUNT(tm) FROM App:TiempoMaquina tm JOIN tm.usuario u JOIN tm.pc p WHERE  (u.id= :usuario OR p.id= :pc) AND :fechainicio >= tm.fechaInicio AND :fechainicio <= tm.fechaFin AND tm.id!= :id";
                $parameters['id'] = $id;
            }
            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $this->context->buildViolation('tiempomaquina_error_existe(%from%)')
                    ->setTranslationDomain('messages')
                    ->setParameter('%from%', $fechaInicio->format('d-m-Y'))
                    ->atPath('fechainicio')
                    ->addViolation();
            }


        } else {
            $parameters['fechafin'] = $fechaFin;
            if (!$id) {
                $cadena = "SELECT COUNT(tm) FROM App:TiempoMaquina tm JOIN tm.usuario u JOIN tm.pc p WHERE  (u.id= :usuario OR p.id= :pc) AND((:fechainicio <= tm.fechaInicio AND :fechafin>=tm.fechaInicio) OR (:fechainicio >= tm.fechaInicio AND :fechafin<=tm.fechaFin) OR( :fechainicio<=tm.fechaFin AND :fechafin>=tm.fechaFin))";
            } else {
                $cadena = "SELECT COUNT(tm) FROM App:TiempoMaquina tm JOIN tm.usuario u JOIN tm.pc p WHERE tm.id!= :id AND (u.id= :usuario OR p.id= :pc) AND((:fechainicio <= tm.fechaInicio AND :fechafin>=tm.fechaInicio) OR (:fechainicio >= tm.fechaInicio AND :fechafin<=tm.fechaFin) OR( :fechainicio<=tm.fechaFin AND :fechafin>=tm.fechaFin))";
                $parameters['id'] = $id;
            }
            $consulta = $em->createQuery($cadena);
            $consulta->setParameters($parameters);
            $result = $consulta->getResult();
            if ($result[0][1] > 0) {
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('messages')
                    ->setParameter('%from%', $fechaInicio->format('d-m-Y H:i'))
                    ->setParameter('%to%', $fechaFin->format('d-m-Y H:i'))
                    ->atPath('fechainicio')
                    ->addViolation();

            }
        }


    }


}
