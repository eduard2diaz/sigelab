<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\TiempoMaquina as TiempoMaquinaConstraint;

/**
 * TiempoMaquina
 *
 * @ORM\Table(name="tiempomaquina")
 * @ORM\Entity
 * @TiempoMaquinaConstraint(from="fechaInicio",to="fechaFin")
 */
class TiempoMaquina
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="reservacionpc_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechainicio", type="datetime", nullable=false)
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafin", type="datetime", nullable=true)
     */
    private $fechaFin;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $usuario;

    /**
     * @var \Pc
     *
     * @ORM\ManyToOne(targetEntity="Pc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pc", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $pc;

    /**
     * @var \Laboratorio
     *
     * @ORM\ManyToOne(targetEntity="Laboratorio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="laboratorio", referencedColumnName="id",onDelete="Cascade")
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
     * Set usuario
     *
     * @param \App\Entity\Usuario $usuario
     *
     * @return TiempoMaquina
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
     * Set pc
     *
     * @param \App\Entity\Pc $pc
     *
     * @return TiempoMaquina
     */
    public function setPc(\App\Entity\Pc $pc = null)
    {
        $this->pc = $pc;

        return $this;
    }

    /**
     * Get pc
     *
     * @return \App\Entity\Pc
     */
    public function getPc()
    {
        return $this->pc;
    }

    /**
     * @return \Laboratorio
     */
    public function getLaboratorio()
    {
        return $this->laboratorio;
    }

    /**
     * @param \Laboratorio $laboratorio
     */
    public function setLaboratorio(Laboratorio $laboratorio): void
    {
        $this->laboratorio = $laboratorio;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(?\DateTimeInterface $fechaInicio): void
    {
        $this->fechaInicio = $fechaInicio;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): void
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * @Assert\Callback
     */
    public function comprobarFechas(ExecutionContextInterface $context)
    {
        if (null == $this->getFechaInicio()){
            $context->buildViolation('This value should not be blank.')->atPath('fechaInicio')->addViolation();
        }
        elseif (null != $this->getFechaFin() && $this->getFechaFin()<$this->getFechaInicio()){
            $context->buildViolation('reservationlaboratorio_error_startdate_superior')->atPath('fechaFin')->addViolation();
        }

        if (null == $this->getLaboratorio()) {
            $context->buildViolation('This value should not be blank.')->atPath('usuario')->addViolation();
        }

        if (null == $this->getLaboratorio()) {
            $context->buildViolation('This value should not be blank.')->atPath('laboratorio')->addViolation();
        }else
        if (null == $this->getPc()) {
            $context->buildViolation('This value should not be blank.')->atPath('pc')->addViolation();
        }

    }
}
