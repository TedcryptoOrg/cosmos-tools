<?php

namespace App\Entity\Tools;

use App\Entity\Export\ExportProcess;
use App\Enum\Export\ExportStatusEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="export_delegations_request")
 */
class ExportDelegationsRequest
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Export\ExportProcess", inversedBy="exportDelegationsRequest")
     *
     * @ORM\JoinColumn(name="export_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private ?ExportProcess $exportProcess = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $network;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $apiClient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $height;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $status = ExportStatusEnum::PENDING;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $token;

    /**
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     */
    private ?string $error = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->token = bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ExportDelegationsRequest
    {
        $this->id = $id;

        return $this;
    }

    public function getExportProcess(): ?ExportProcess
    {
        return $this->exportProcess;
    }

    public function setExportProcess(?ExportProcess $exportProcess): ExportDelegationsRequest
    {
        $this->exportProcess = $exportProcess;

        return $this;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network): ExportDelegationsRequest
    {
        $this->network = $network;

        return $this;
    }

    public function getApiClient(): ?string
    {
        return $this->apiClient;
    }

    public function setApiClient(?string $apiClient): ExportDelegationsRequest
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): ExportDelegationsRequest
    {
        $this->email = $email;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): ExportDelegationsRequest
    {
        $this->height = $height;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): ExportDelegationsRequest
    {
        $this->status = $status;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): ExportDelegationsRequest
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): ExportDelegationsRequest
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): ExportDelegationsRequest
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error)
    {
        $this->error = $error;

        return $this;
    }
}
