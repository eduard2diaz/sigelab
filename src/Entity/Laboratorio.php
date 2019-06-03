<?php

namespace App\Entity;

use App\Entity\Facultad;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Laboratorio
 *
 * @ORM\Table(name="laboratorio", indexes={@ORM\Index(name="IDX_9D3D6DD6F50454DFA", columns={"nombre"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre","facultad"})
 */
class Laboratorio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="laboratorio_id_seq", allocationSize=1, initialValue=1)
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
     * @var boolean
     *
     * @ORM\Column(name="enfuncionamiento", type="boolean", nullable=true)
     */
    private $enfuncionamiento;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Facultad", mappedBy="idlaboratorio")
     */
    private $idfacultad;

    /**
     * @var \Facultad
     *
     * @ORM\ManyToOne(targetEntity="Facultad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facultad", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $facultad;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idfacultad = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enfuncionamiento=true;
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
     * @return Laboratorio
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
     * Set enfuncionamiento
     *
     * @param boolean $enfuncionamiento
     *
     * @return Laboratorio
     */
    public function setEnfuncionamiento($enfuncionamiento)
    {
        $this->enfuncionamiento = $enfuncionamiento;

        return $this;
    }

    /**
     * Get enfuncionamiento
     *
     * @return boolean
     */
    public function getEnfuncionamiento()
    {
        return $this->enfuncionamiento;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdfacultad(): \Doctrine\Common\Collections\Collection
    {
        return $this->idfacultad;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idfacultad
     */
    public function setIdfacultad(\Doctrine\Common\Collections\Collection $idfacultad): void
    {
        $this->idfacultad = $idfacultad;
    }

    /**
     * Add idfacultad
     *
     * @param \App\Entity\Facultad $idfacultad
     *
     * @return Laboratorio
     */
    public function addIdfacultad(Facultad $idfacultad)
    {
        $this->idfacultad[] = $idfacultad;

        return $this;
    }

    /**
     * Remove idfacultad
     *
     * @param \App\Entity\Facultad $idfacultad
     */
    public function removeIdfacultad(Facultad $idfacultad)
    {
        $this->idfacultad->removeElement($idfacultad);
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * @return \Facultad
     */
    public function getFacultad(): ?Facultad
    {
        return $this->facultad;
    }

    /**
     * @param \Facultad $facultad
     */
    public function setFacultad(Facultad $facultad): void
    {
        $this->facultad = $facultad;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null == $this->getFacultad()) {
            /*   $context->setNode($context, 'facultad', null, 'data.facultad');
               $context->addViolation('Seleccione una facultad.');
            OTRA FORMA DE AGREGAR EL ERROR SERIA
            */
            $context->buildViolation('faculty_field_select')
                ->atPath('facultad')
                ->addViolation();
       }
    }
}
