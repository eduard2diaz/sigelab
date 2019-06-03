<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Facultad
 *
 * @ORM\Table(name="facultad")
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Facultad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="facultad_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\ManyToMany(targetEntity="Laboratorio", inversedBy="idfacultad")
     * @ORM\JoinTable(name="laboratoriofacultad",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idfacultad", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idlaboratorio", referencedColumnName="id")
     *   }
     * )
     */
    private $idlaboratorio;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idlaboratorio = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Facultad
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

    public function __toString()
    {
       return $this->getNombre();
    }

    /**
     * Add idlaboratorio
     *
     * @param \App\Entity\Laboratorio $idlaboratorio
     *
     * @return Facultad
     */
    public function addIdlaboratorio(\App\Entity\Laboratorio $idlaboratorio)
    {
        $this->idlaboratorio[] = $idlaboratorio;

        return $this;
    }

    /**
     * Remove idlaboratorio
     *
     * @param \App\Entity\Laboratorio $idlaboratorio
     */
    public function removeIdlaboratorio(\App\Entity\Laboratorio $idlaboratorio)
    {
        $this->idlaboratorio->removeElement($idlaboratorio);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdlaboratorio(): \Doctrine\Common\Collections\Collection
    {
        return $this->idlaboratorio;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idlaboratorio
     */
    public function setIdlaboratorio(\Doctrine\Common\Collections\Collection $idlaboratorio): void
    {
        $this->idlaboratorio = $idlaboratorio;
    }


}
