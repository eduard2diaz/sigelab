<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReservacionLaboratorio extends Constraint
{
    public $message = 'reservationlaboratorio_error_range_existe(%laboratorio%,%from%,%to%)';
    public $service = 'reservacionlaboratorio.validator.period';
    public $em = null;
    public $repositoryMethod = 'findBy';
    public $from;
    public $to;
    public $errorPath = 'from';
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return ['from','to'];
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return $this->service;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'from';
    }

}
