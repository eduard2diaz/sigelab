<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservacion
 *
 * @ORM\Table(name="reservacionpc")
 * @ORM\Entity
 * @UniqueEntity(fields={"fecha","usuario"},message="reservacionpc_error_userjustreserv")
 */
class ReservacionPc
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
     * @ORM\Column(name="fechainicio", type="date", nullable=true)
     */
    private $fecha;

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

    public function __construct()
    {
        $this->setFecha(new \DateTime());
    }

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
     * @return \DateTime
     */
    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha(\DateTime $fecha): void
    {
        $this->fecha = $fecha;
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
     * Set pc
     *
     * @param \App\Entity\Pc $pc
     *
     * @return ReservacionPc
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



    /**
     * @Assert\Callback
     */
    public function comprobarFechas(ExecutionContextInterface $context)
    {
        if (null == $this->getFecha()){
            $context->buildViolation('This value should not be blank.')->atPath('fecha')->addViolation();
        }

        if (null == $this->getLaboratorio()) {
            $context->buildViolation('reservationlaboratorio_error_laboratorio_blank')->atPath('laboratorio')->addViolation();
        }

    }
}
