<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reparacion
 *
 * @ORM\Table(name="reparacion")
 * @ORM\Entity
 */
class Reparacion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="reparacion_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="float", precision=10, scale=0)
     * @Assert\Range(min=1)
     */
    private $precio;

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
     * @var \Pc
     *
     * @ORM\ManyToOne(targetEntity="Pc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pc", referencedColumnName="id")
     * })
     */
    private $pc;

    /**
     * @var \Tiporeparacion
     *
     * @ORM\ManyToOne(targetEntity="Tiporeparacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiporeparacion", referencedColumnName="id")
     * })
     */
    private $tiporeparacion;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Reparacion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Reparacion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set precio
     *
     * @param float $precio
     *
     * @return Reparacion
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return float
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set usuario
     *
     * @param \App\Entity\Usuario $usuario
     *
     * @return Reparacion
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
     * @return Reparacion
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
     * Set tiporeparacion
     *
     * @param \App\Entity\Tiporeparacion $tiporeparacion
     *
     * @return Reparacion
     */
    public function setTiporeparacion(\App\Entity\Tiporeparacion $tiporeparacion = null)
    {
        $this->tiporeparacion = $tiporeparacion;

        return $this;
    }

    /**
     * Get tiporeparacion
     *
     * @return \App\Entity\Tiporeparacion
     */
    public function getTiporeparacion()
    {
        return $this->tiporeparacion;
    }
}
