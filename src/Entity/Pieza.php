<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Pieza
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Propiedad", inversedBy="idpieza")
     * @ORM\JoinTable(name="piezapropiedad",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idpieza", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idpropiedad", referencedColumnName="id")
     *   }
     * )
     */
    private $idpropiedad;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Periferico", mappedBy="idpieza")
     */
    private $idperiferico;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idpropiedad = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idperiferico = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add idpropiedad
     *
     * @param \App\Entity\Propiedad $idpropiedad
     *
     * @return Pieza
     */
    public function addIdpropiedad(\App\Entity\Propiedad $idpropiedad)
    {
        $this->idpropiedad[] = $idpropiedad;

        return $this;
    }

    /**
     * Remove idpropiedad
     *
     * @param \App\Entity\Propiedad $idpropiedad
     */
    public function removeIdpropiedad(\App\Entity\Propiedad $idpropiedad)
    {
        $this->idpropiedad->removeElement($idpropiedad);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdpropiedad(): \Doctrine\Common\Collections\Collection
    {
        return $this->idpropiedad;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idpropiedad
     */
    public function setIdpropiedad(\Doctrine\Common\Collections\Collection $idpropiedad): void
    {
        $this->idpropiedad = $idpropiedad;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Add idperiferico
     *
     * @param \App\Entity\Periferico $idperiferico
     *
     * @return Pieza
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

    public function __toString()
    {
       return $this->getNombre();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getIdpropiedad()->isEmpty()) {
            $context->buildViolation('piece_form_property_error')
                ->atPath('idpropiedad')
                ->addViolation();
        }
    }
}
