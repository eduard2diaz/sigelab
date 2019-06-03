<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Facultad;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UsuarioRepository")
 * @UniqueEntity(fields={"usuario"})
 * @UniqueEntity(fields={"correo"})
 */
class Usuario implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="usuario_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $correo;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    private $salt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Rol", inversedBy="idusuario")
     * @ORM\JoinTable(name="usuariorol",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idusuario", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idrol", referencedColumnName="id")
     *   }
     * )
     */
    private $idrol;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Mensaje", mappedBy="iddestinatario")
     */
    private $idmensaje;

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
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogin", type="datetime", nullable=true)
     */
    private $lastlogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogout", type="datetime", nullable=true)
     */
    private $lastlogout;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rutaFoto;

    /**
     * @Assert\Image()
     */
    private $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idrol = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idmensaje = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activo=true;
        $this->salt=uniqid();
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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Usuario
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
     * Set salt
     *
     * @param string $salt
     *
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Add idrol
     *
     * @param \App\Entity\Rol $idrol
     *
     * @return Usuario
     */
    public function addIdrol(\App\Entity\Rol $idrol)
    {
        $this->idrol[] = $idrol;

        return $this;
    }

    /**
     * Remove idrol
     *
     * @param \App\Entity\Rol $idrol
     */
    public function removeIdrol(\App\Entity\Rol $idrol)
    {
        $this->idrol->removeElement($idrol);
    }

    /**
     * Get idrol
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdrol()
    {
        return $this->idrol;
    }

    /**
     * Add idmensaje
     *
     * @param \App\Entity\Mensaje $idmensaje
     *
     * @return Usuario
     */
    public function addIdmensaje(\App\Entity\Mensaje $idmensaje)
    {
        $this->idmensaje[] = $idmensaje;

        return $this;
    }

    /**
     * Remove idmensaje
     *
     * @param \App\Entity\Mensaje $idmensaje
     */
    public function removeIdmensaje(\App\Entity\Mensaje $idmensaje)
    {
        $this->idmensaje->removeElement($idmensaje);
    }

    /**
     * Get idmensaje
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdmensaje()
    {
        return $this->idmensaje;
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
     * @return string
     */
    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    /**
     * @param string $apellido
     */
    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }

    /**
     * @return string
     */
    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    /**
     * @param string $correo
     */
    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    /**
     * @return \DateTime
     */
    public function getLastlogin(): \DateTime
    {
        return $this->lastlogin;
    }

    /**
     * @param \DateTime $lastlogin
     */
    public function setLastlogin($lastlogin=null): void
    {
        $this->lastlogin = $lastlogin;
    }

    /**
     * @return \DateTime
     */
    public function getLastlogout()
    {
        return $this->lastlogout;
    }

    /**
     * @param \DateTime $lastlogout
     */
    public function setLastlogout($lastlogout): void
    {
        $this->lastlogout = $lastlogout;
    }

    /**
     * @return mixed
     */
    public function getRutaFoto()
    {
        return $this->rutaFoto;
    }

    /**
     * @param mixed $rutaFoto
     */
    public function setRutaFoto($rutaFoto): void
    {
        $this->rutaFoto = $rutaFoto;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }


    public function getRoles()
    {
        $array=array();
        foreach ($this->getIdrol()->toArray() as $value)
            $array[]=$value->getNombre();
        return $array;
    }

    public function getUsername()
    {
        return $this->getUsuario();
    }

    public function eraseCredentials()
    {
    }

    /**
     * @Assert\Callback
     */
    public function validar(ExecutionContextInterface $context)
    {
        if (null==$this->getFacultad()) {
            $context->setNode($context, 'facultad', null, 'data.facultad');
            $context->addViolation('faculty_field_select');
        }

        if ($this->getIdrol()->isEmpty()) {
            $context->setNode($context, 'idrol', null, 'data.idrol');
            $context->addViolation('user_form_rol_error_blank');
        }

        $roles=$this->getRoles();
        if(in_array('ROLE_TECNICO',$roles) && in_array('ROLE_JEFETECNICO',$roles)){
            $context->setNode($context, 'idrol', null, 'data.idrol');
            $context->addViolation('user_form_rol_error_incompatibility');
        }elseif(in_array('ROLE_TECNICO',$roles) && in_array('ROLE_JEFETECNICOINSTITUCIONAL',$roles)){
            $context->setNode($context, 'idrol', null, 'data.idrol');
            $context->addViolation('user_form_rol_error_incompatibility');
        }elseif(in_array('ROLE_JEFETECNICO',$roles) && in_array('ROLE_JEFETECNICOINSTITUCIONAL',$roles)){
            $context->setNode($context, 'idrol', null, 'data.idrol');
            $context->addViolation('user_form_rol_error_incompatibility');
        }elseif(in_array('ROLE_PROFESOR',$roles) && in_array('ROLE_ESTUDIANTE',$roles)){
            $context->setNode($context, 'idrol', null, 'data.idrol');
            $context->addViolation('user_form_rol_error_incompatibility');
        }

    }

    public function __toString()
    {
       return $this->getNombre();
    }

    public function getFullName()
    {
       return $this->getNombre().' '.$this->getApellido();
    }
}
