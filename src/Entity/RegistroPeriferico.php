<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Pieza;
use App\Entity\Periferico;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * RegistroPieza
 *
 * @ORM\Table(name="registroperiferico")
 * @ORM\Entity
 */
class RegistroPeriferico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="registro_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $valor;

    /**
     * @var \Propiedad
     *
     * @ORM\ManyToOne(targetEntity="Propiedad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="propiedad", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $propiedad;

    /**
     * @var \Periferico
     *
     * @ORM\ManyToOne(targetEntity="Periferico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="periferico", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $periferico;

    /**
     * @var \Pieza
     *
     * @ORM\ManyToOne(targetEntity="Pieza")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pieza", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $pieza;

    /**
     * @var \Pc
     *
     * @ORM\ManyToOne(targetEntity="Pc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pc", referencedColumnName="id")
     * })
     */
    private $pc;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return RegistroPeriferico
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
     * Set valor
     *
     * @param string $valor
     *
     * @return RegistroPeriferico
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set propiedad
     *
     * @param \App\Entity\Propiedad $propiedad
     *
     * @return RegistroPeriferico
     */
    public function setPropiedad(\App\Entity\Propiedad $propiedad = null)
    {
        $this->propiedad = $propiedad;

        return $this;
    }

    /**
     * Get propiedad
     *
     * @return \App\Entity\Propiedad
     */
    public function getPropiedad()
    {
        return $this->propiedad;
    }

    /**
     * Set pc
     *
     * @param \App\Entity\Pc $pc
     *
     * @return RegistroPeriferico
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
     * @return \Periferico
     */
    public function getPeriferico(): ?Periferico
    {
        return $this->periferico;
    }

    /**
     * @param \Periferico $periferico
     */
    public function setPeriferico(Periferico $periferico): void
    {
        $this->periferico = $periferico;
    }

    /**
     * @return \Pieza
     */
    public function getPieza(): ?Pieza
    {
        return $this->pieza;
    }

    /**
     * @param \Pieza $pieza
     */
    public function setPieza(Pieza $pieza=null): void
    {
        $this->pieza = $pieza;
    }

    /**
     * @Assert\Callback
     */
    public function validar(ExecutionContextInterface $context)
    {
        if (null == $this->getPeriferico()) {
            $context->setNode($context, 'periferico', null, 'data.periferico');
            $context->addViolation('peripheralregister_form_peripheral_error');
        }
        if (null == $this->getPropiedad()) {
            $context->setNode($context, 'propiedad', null, 'data.propiedad');
            $context->addViolation('piece_form_property_error');
        }
        if (null == $this->getPc()) {
            $context->setNode($context, 'pc', null, 'data.pc');
            $context->addViolation('Seleccione una pc');
        }
    }
}
