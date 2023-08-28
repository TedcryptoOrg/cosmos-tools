<?php

namespace App\Entity\Export;

use App\Enum\Export\ExportStatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(
 *     indexes={
 *
 *      @ORM\Index(name="validator_address", columns={"validator_address"})
 *     }
 * )
 */
class Validator
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="ExportProcess")
     *
     * @ORM\JoinColumn(name="export_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ExportProcess $exportProcess;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $validatorName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $validatorAddress;

    /**
     * @ORM\Column(name="is_completed", type="boolean", options={"default": false})
     */
    private bool $isCompleted = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $completedAt = null;

    /**
     * @ORM\Column(name="status", type="string")
     */
    private string $status = ExportStatusEnum::PENDING;

    /**
     * @ORM\Column(name="is_error", type="boolean", options={"default": false})
     */
    private bool $isError = false;

    /**
     * @ORM\Column(name="error_message", type="text", nullable=true)
     */
    private ?string $errorMessage = null;

    /**
     * @var array|Collection|Delegation[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Export\Delegation", mappedBy="validator", cascade={"all"})
     */
    private array|Collection $delegations;

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
        $this->delegations = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Validator
    {
        $this->id = $id;

        return $this;
    }

    public function getExportProcess(): ExportProcess
    {
        return $this->exportProcess;
    }

    public function setExportProcess(ExportProcess $exportProcess): Validator
    {
        $this->exportProcess = $exportProcess;

        return $this;
    }

    public function getValidatorName(): string
    {
        return $this->validatorName;
    }

    public function setValidatorName(string $validatorName): Validator
    {
        $this->validatorName = $validatorName;

        return $this;
    }

    public function getValidatorAddress(): string
    {
        return $this->validatorAddress;
    }

    public function setValidatorAddress(string $validatorAddress): Validator
    {
        $this->validatorAddress = $validatorAddress;

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): Validator
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTime $completedAt): Validator
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return array|Collection|Delegation[]
     */
    public function getDelegations(): array|Collection
    {
        return $this->delegations;
    }

    public function setDelegations(array|Collection $delegations): Validator
    {
        $this->delegations = new ArrayCollection();
        foreach ($delegations as $delegation) {
            $this->addDelegation($delegation);
        }

        return $this;
    }

    public function addDelegation(Delegation $delegation): Validator
    {
        if (!$this->delegations->contains($delegation)) {
            $delegation->setValidator($this);
            $this->delegations->add($delegation);
        }

        return $this;
    }

    public function removeDelegation(Delegation $delegation): Validator
    {
        if ($this->delegations->contains($delegation)) {
            $this->delegations->removeElement($delegation);
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Validator
    {
        $this->status = $status;

        return $this;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function setIsError(bool $isError): Validator
    {
        $this->isError = $isError;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): Validator
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Validator
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Validator
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
