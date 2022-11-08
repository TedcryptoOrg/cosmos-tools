<?php

namespace App\Entity\Export;

use App\Entity\Tools\ExportDelegationsRequest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ExportProcess
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $network;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $height;

    /**
     * @ORM\Column(type="boolean", length=255, options={"default": false})
     */
    private bool $isCompleted = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $completedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @var ExportDelegationsRequest[]|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Tools\ExportDelegationsRequest", mappedBy="exportProcess", cascade={"all"})
     */
    private array|Collection $exportDelegationsRequests;

    /**
     * @var Validator[]|Collection|array
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Export\Validator", mappedBy="exportProcess", cascade={"all"})
     */
    private array|Collection $validators;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->exportDelegationsRequests = new ArrayCollection();
        $this->validators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ExportProcess
    {
        $this->id = $id;
        return $this;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function setNetwork(string $network): ExportProcess
    {
        $this->network = $network;
        return $this;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function setHeight(string $height): ExportProcess
    {
        $this->height = $height;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): ExportProcess
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    public function getCompletedAt(): \DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTime $completedAt): ExportProcess
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): ExportProcess
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return ExportDelegationsRequest[]|array|Collection
     */
    public function getExportDelegationsRequests(): array|Collection
    {
        return $this->exportDelegationsRequests;
    }

    public function setExportDelegationsRequests(array|Collection $exportDelegationsRequests): ExportProcess
    {
        foreach ($exportDelegationsRequests as $exportDelegationsRequestItem) {
            $this->addExportDelegationsRequest($exportDelegationsRequestItem);
        }

        return $this;
    }

    public function addExportDelegationsRequest(ExportDelegationsRequest $exportDelegationsRequest): ExportProcess
    {
        $exportDelegationsRequest->setExportProcess($this);
        $this->exportDelegationsRequests->add($exportDelegationsRequest);

        return $this;
    }

    public function removeExportDelegationsRequest(ExportDelegationsRequest $exportDelegationsRequest): ExportProcess
    {
        if ($this->exportDelegationsRequests->contains($exportDelegationsRequest)) {
            $this->exportDelegationsRequests->removeElement($exportDelegationsRequest);
        }

        return $this;
    }

    /**
     * @return Validator[]|array|Collection
     */
    public function getValidators(): array|Collection
    {
        return $this->validators;
    }

    public function setValidators(array|Collection $validators): ExportProcess
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    public function addValidator(Validator $validator): ExportProcess
    {
        if (!$this->validators->contains($validator)) {
            $validator->setExportProcess($this);
            $this->validators->add($validator);
        }

        return $this;
    }

    public function removeValidator(Validator $validator): ExportProcess
    {
        if ($this->validators->contains($validator)) {
            $this->validators->removeElement($validator);
        }

        return $this;
    }
}