<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="mensaje", indexes={@ORM\Index(name="IDX_729A19EC51A5ACA4", columns={"remitente"})})
 * @ORM\Entity
 */
class Mensaje
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mensaje_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="noleida", type="boolean", nullable=true)
     */
    private $noleida;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="remitente", referencedColumnName="id")
     * })
     */
    private $remitente;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="propietario", referencedColumnName="id")
     * })
     */
    private $propietario;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="idmensaje")
     * @ORM\JoinTable(name="mensajeusuario",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idmensaje", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="iddestinatario", referencedColumnName="id")
     *   }
     * )
     */
    private $iddestinatario;

    /**
     * @var integer
     *
     * @ORM\Column(name="bandeja", type="integer", nullable=false)
     */
    private $bandeja;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isnew;

    public function __construct()
    {
        $this->fecha=new \DateTime();
        $this->iddestinatario = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setNoleida(true);
        $this->bandeja=1;
        $this->isnew=true;
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
     * @return Notificacion
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
     * @return Notificacion
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
     * Set noleida
     *
     * @param boolean $noleida
     *
     * @return Notificacion
     */
    public function setNoleida($noleida)
    {
        $this->noleida = $noleida;

        return $this;
    }

    /**
     * Get noleida
     *
     * @return boolean
     */
    public function getNoleida()
    {
        return $this->noleida;
    }

    /**
     * Set remitente
     *
     * @param \App\Entity\Usuario $remitente
     *
     * @return Notificacion
     */
    public function setRemitente(\App\Entity\Usuario $remitente = null)
    {
        $this->remitente = $remitente;

        return $this;
    }

    /**
     * Get remitente
     *
     * @return \App\Entity\Usuario
     */
    public function getRemitente()
    {
        return $this->remitente;
    }

    /**
     * Set propietario
     *
     * @param \App\Entity\Usuario $propietario
     *
     * @return Notificacion
     */
    public function setPropietario(\App\Entity\Usuario $propietario = null)
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * Get propietario
     *
     * @return \App\Entity\Usuario
     */
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Add iddestinatario
     *
     * @param \App\Entity\Usuario $iddestinatario
     *
     * @return Mensaje
     */
    public function addIddestinatario(\App\Entity\Usuario $iddestinatario)
    {
        $this->iddestinatario[] = $iddestinatario;

        return $this;
    }

    /**
     * Remove iddestinatario
     *
     * @param \App\Entity\Usuario $iddestinatario
     */
    public function removeIddestinatario(\App\Entity\Usuario $iddestinatario)
    {
        $this->iddestinatario->removeElement($iddestinatario);
    }

    /**
     * Get iddestinatario
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIddestinatario()
    {
        return $this->iddestinatario;
    }

    /**
     * @return int
     */
    public function getBandeja(): int
    {
        return $this->bandeja;
    }

    /**
     * @param int $bandeja
     */
    public function setBandeja(int $bandeja): void
    {
        $this->bandeja = $bandeja;
    }

    public function getIsnew(): ?bool
    {
        return $this->isnew;
    }

    public function setIsnew(bool $isnew): self
    {
        $this->isnew = $isnew;

        return $this;
    }
}
