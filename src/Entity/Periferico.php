<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"nombre"})
 */
class Periferico
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
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Propiedad", inversedBy="idperiferico")
     * @ORM\JoinTable(name="perifericopropiedad",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idperiferico", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="Pieza", inversedBy="idperiferico")
     * @ORM\JoinTable(name="perifericopieza",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idperiferico", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idpieza", referencedColumnName="id")
     *   }
     * )
     */
    private $idpieza;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idpropiedad = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idpieza = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add idpropiedad
     *
     * @param \App\Entity\Propiedad $idpropiedad
     *
     * @return Periferico
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

    /**
     * Add idpieza
     *
     * @param \App\Entity\Pieza $idpieza
     *
     * @return Periferico
     */
    public function addIdpieza(\App\Entity\Pieza $idpieza)
    {
        $this->idpieza[] = $idpieza;

        return $this;
    }

    /**
     * Remove idpieza
     *
     * @param \App\Entity\Pieza $idpieza
     */
    public function removeIdpieza(\App\Entity\Pieza $idpieza)
    {
        $this->idpieza->removeElement($idpieza);
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
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
