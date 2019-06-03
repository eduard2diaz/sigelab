<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Propiedad
 *
 * @ORM\Table(name="propiedad")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Propiedad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="propiedad_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Periferico", mappedBy="idpropiedad")
     */
    private $idperiferico;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Pieza", mappedBy="idpropiedad")
     */
    private $idpieza;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idperiferico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idpieza = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Propiedad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add idperiferico
     *
     * @param \App\Entity\Periferico $idperiferico
     *
     * @return Propiedad
     */
    public function addIdperiferico(\App\Entity\Periferico $idperiferico)
    {
        $this->idperiferico[] = $idperiferico;

        return $this;
    }

    /**
     * Remove idperiferico
     *
     * @param \App\Entity\Periferico $idperiferico
     */
    public function removeIdperiferico(\App\Entity\Periferico $idperiferico)
    {
        $this->idperiferico->removeElement($idperiferico);
    }

    /**
     * Add idpieza
     *
     * @param \App\Entity\Pieza $idpieza
     *
     * @return Propiedad
     */
    public function addIdpieza(\App\Entity\Pieza $idpieza)
    {
        $this->idpieza[] = $idpieza;

        return $this;
    }

    /**
     * Remove idpieza
     *
     * @param \App\Entity\Periferico $idpieza
     */
    public function removeIdpieza(\App\Entity\Pieza $idpieza)
    {
        $this->idpieza->removeElement($idpieza);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdperiferico(): \Doctrine\Common\Collections\Collection
    {
        return $this->idperiferico;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idperiferico
     */
    public function setIdperiferico(\Doctrine\Common\Collections\Collection $idperiferico): void
    {
        $this->idperiferico = $idperiferico;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdpieza(): \Doctrine\Common\Collections\Collection
    {
        return $this->idpieza;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idpieza
     */
    public function setIdpieza(\Doctrine\Common\Collections\Collection $idpieza): void
    {
        $this->idpieza = $idpieza;
    }

    public function __toString()
    {
     return $this->getNombre();
    }
}
