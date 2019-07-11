<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tokens")
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\Column(name="data", type="text", nullable=false)
     */
    private $data;

    /**
     * @ORM\Column(name="created_at", type="string", length=30, nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="expires_at", type="string", length=30, nullable=false)
     */
    private $expiresAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tokens", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setExpiresAt(string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}