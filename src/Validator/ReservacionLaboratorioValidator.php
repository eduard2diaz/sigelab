<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Debug\Debug;

class ReservacionLaboratorioValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {

        /* @var $constraint App\Validator\ReservacionLaboratorio */
        $pa = PropertyAccess::createPropertyAccessor();

        if (!$constraint instanceof ReservacionLaboratorio) {
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
        $laboratorio = $pa->getValue($value, 'laboratorio');

        if (!is_string($constraint->to)) {
            throw new UnexpectedTypeException($constraint->to, 'string');
        } else
            if (!$class->hasField($constraint->to) && !$class->hasAssociation($constraint->to))
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $constraint->to));

        $fechaFin = $pa->getValue($value, $constraint->to);
        $id = $pa->getValue($value, 'id');

        $parameters = [
            'fechainicio' => $fechaInicio,
            'fechafin' => $fechaFin,
            'laboratorio'=>$laboratorio->getId()
        ];
        if (!$id) {
            $cadena = "SELECT COUNT(r) FROM App:ReservacionLaboratorio r JOIN r.laboratorio l WHERE  l.id = :laboratorio AND((:fechainicio <= r.fechainicio AND :fechafin>=r.fechainicio) OR (:fechainicio >= r.fechainicio AND :fechafin<=r.fechafin) OR( :fechainicio<=r.fechafin AND :fechafin>=r.fechafin))";
        } else {
            $cadena = "SELECT COUNT(r) FROM App:ReservacionLaboratorio r JOIN r.laboratorio l WHERE r.id!= :id AND l.id = :laboratorio AND((:fechainicio <= r.fechainicio AND :fechafin>=r.fechainicio) OR (:fechainicio >= r.fechainicio AND :fechafin<=r.fechafin) OR( :fechainicio<=r.fechafin AND :fechafin>=r.fechafin))";
            $parameters['id'] = $id;
        }
        $consulta = $em->createQuery($cadena);
        $consulta->setParameters($parameters);
        $result = $consulta->getResult();
        if ($result[0][1] > 0) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('messages')
                ->setParameter('%from%', $fechaInicio->format('d-m-Y'))
                ->setParameter('%to%',  $fechaFin->format('d-m-Y'))
                ->setParameter('%laboratorio%', $laboratorio->getNombre())
                ->atPath('fechainicio')
                ->addViolation();

        }


    }


}
