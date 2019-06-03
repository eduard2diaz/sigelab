<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Laboratorio;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Pc
 *
 * @ORM\Table(name="pc")
 * @ORM\Entity
 * @ORM\Entity
 * @UniqueEntity(fields={"numero","laboratorio"})
 * @UniqueEntity(fields={"mac"})
 */
class Pc
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="pc_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-f0-9]{2}[:-][a-f0-9]{2}[:-][a-f0-9]{2}[:-][a-f0-9]{2}[:-][a-f0-9]{2}[:-][a-f0-9]{2}$/i")
     */
    private $mac;

    /**
     * @var \Laboratorio
     *
     * @ORM\ManyToOne(targetEntity="Laboratorio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="laboratorio", referencedColumnName="id",onDelete="Cascade")
     * })
     */
    private $laboratorio;

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
     * @return string
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return integer
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoToString()
    {
        $array=['pc_state_exploitation','pc_state_pendingmaintenance','pc_state_pendingcancel'];
        return $array[$this->estado];
    }

    /**
     * @param integer $estado
     */
    public function setEstado($estado): void
    {
        $this->estado = $estado;
    }

    /**
     * @return \Laboratorio
     */
    public function getLaboratorio(): ?Laboratorio
    {
        return $this->laboratorio;
    }

    /**
     * @return string
     */
    public function getMac(): ?string
    {
        return $this->mac;
    }

    /**
     * @param string $mac
     */
    public function setMac($mac): void
    {
        $this->mac = $mac;
    }

    /**
     * @param \Laboratorio $laboratorio
     */
    public function setLaboratorio(Laboratorio $laboratorio): void
    {
        $this->laboratorio = $laboratorio;
    }

    public function __toString()
    {
       return $this->getNumero();
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getLaboratorio()==null) {
            $context->buildViolation('reservationlaboratorio_error_laboratorio_blank')
                ->atPath('laboratorio')
                ->addViolation();
        }
    }

}
