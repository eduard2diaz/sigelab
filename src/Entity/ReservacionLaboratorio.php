<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Validator\ReservacionLaboratorio as ReservacionLaboratorioConstraint;

/**
 * Reservacion
 *
 * @ORM\Table(name="reservacionlaboratorio")
 * @ORM\Entity
 * @ReservacionLaboratorioConstraint(from="fechainicio",to="fechafin")
 */
class ReservacionLaboratorio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="reservacionlaboratorio_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicio", type="datetime")
     */
    private $fechainicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafin", type="datetime")
     */
    private $fechafin;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @var \Laboratorio
     *
     * @ORM\ManyToOne(targetEntity="Laboratorio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="laboratorio", referencedColumnName="id")
     * })
     */
    private $laboratorio;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechainicio
     *
     * @param \DateTime $fechainicio
     *
     * @return ReservacionPc
     */
    public function setFechainicio(\Datetime $fechainicio)
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    /**
     * Get fechainicio
     *
     * @return \DateTime
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * Set fechafin
     *
     * @param \DateTime $fechafin
     *
     * @return ReservacionPc
     */
    public function setFechafin(\Datetime $fechafin)
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    /**
     * Get fechafin
     *
     * @return \DateTime
     */
    public function getFechafin()
    {
        return $this->fechafin;
    }

    /**
     * Set usuario
     *
     * @param \App\Entity\Usuario $usuario
     *
     * @return ReservacionPc
     */
    public function setUsuario(\App\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \App\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set laboratorio
     *
     * @param \App\Entity\Laboratorio $laboratorio
     *
     * @return ReservacionLaboratorio
     */
    public function setLaboratorio(\App\Entity\Laboratorio $laboratorio = null)
    {
        $this->laboratorio = $laboratorio;

        return $this;
    }

    /**
     * Get laboratorio
     *
     * @return \App\Entity\Laboratorio
     */
    public function getLaboratorio()
    {
        return $this->laboratorio;
    }

    /**
     * @Assert\Callback
     */
    public function comprobarFechas(ExecutionContextInterface $context)
    {
        $badstruc=false;
        if (null == $this->getFechainicio()){
            $context->buildViolation('This value should not be blank.')->atPath('fechainicio')->addViolation();
            $badstruc=true;
        }
        elseif ($this->getFechainicio()->format('H') > 17 || $this->getFechainicio()->format('H') < 8)
            $context->buildViolation('reservationlaboratorio_error_time_check')->atPath('fechainicio')->addViolation();

        if (null == $this->getFechafin()){
            $context->buildViolation('This value should not be blank.')->atPath('fechafin')->addViolation();
            $badstruc=true;
        }
        elseif ($this->getFechafin()->format('H') > 17 || $this->getFechafin()->format('H') < 8)
            $context->buildViolation('reservationlaboratorio_error_time_check')->atPath('fechafin')->addViolation();

        if (!$badstruc && $this->getFechafin() < $this->getFechainicio()) {
            $context->buildViolation('reservationlaboratorio_error_startdate_superior')->atPath('fechafin')->addViolation();
        }
        if (null == $this->getLaboratorio()) {
            $context->buildViolation('reservationlaboratorio_error_laboratorio_blank')->atPath('laboratorio')->addViolation();
        }

    }
}
