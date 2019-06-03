<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Rol
 *
 * @ORM\Table(name="rol")
 * @ORM\Entity
 */
class Rol extends Role
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="rol_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=true)
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="idrol")
     */
    private $idusuario;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idusuario = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Rol
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
     * Add idusuario
     *
     * @param \App\Entity\Usuario $idusuario
     *
     * @return Rol
     */
    public function addIdusuario(\App\Entity\Usuario $idusuario)
    {
        $this->idusuario[] = $idusuario;

        return $this;
    }

    /**
     * Remove idusuario
     *
     * @param \App\Entity\Usuario $idusuario
     */
    public function removeIdusuario(\App\Entity\Usuario $idusuario)
    {
        $this->idusuario->removeElement($idusuario);
    }

    /**
     * Get idusuario
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdusuario()
    {
        return $this->idusuario;
    }

    public function __toString()
    {
        $array=['ROLE_ADMIN'=>'Administrador','ROLE_ESTUDIANTE'=>'Estudiante','ROLE_PROFESOR'=>'Profesor',
                'ROLE_TECNICO'=>'Tecnico de laboratorio','ROLE_JEFETECNICO'=>'Jefe de Técnico'
            ,'ROLE_JEFETECNICOINSTITUCIONAL'=>'Jefe de Técnicos Institucional'];
     return $array[$this->getNombre()];
    }

}
