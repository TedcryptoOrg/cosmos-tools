<?php

namespace App\Entity\Export;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(
 *    indexes={
 *
 *      @ORM\Index(name="delegator_address", columns={"delegator_address"})
 *    }
 * )
 */
class Delegation
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
     * @ORM\ManyToOne(targetEntity="Validator")
     *
     * @ORM\JoinColumn(name="validator_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Validator $validator;

    /**
     * @ORM\Column(name="delegator_address", type="string", length=255)
     */
    private string $delegatorAddress;

    /**
     * @ORM\Column(name="shares", type="string", length=255)
     */
    private string $shares;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Delegation
    {
        $this->id = $id;

        return $this;
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    public function setValidator(Validator $validator): Delegation
    {
        $this->validator = $validator;

        return $this;
    }

    public function getDelegatorAddress(): string
    {
        return $this->delegatorAddress;
    }

    public function setDelegatorAddress(string $delegatorAddress): Delegation
    {
        $this->delegatorAddress = $delegatorAddress;

        return $this;
    }

    public function getShares(): string
    {
        return $this->shares;
    }

    public function setShares(string $shares): Delegation
    {
        $this->shares = $shares;

        return $this;
    }
}
