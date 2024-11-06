<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column(length: 255)]
    private ?string $serverName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $sendingDate = null;

    #[ORM\Column(length: 5)]
    private ?string $extension = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Subcategory>
     */
    #[ORM\ManyToMany(targetEntity: Subcategory::class, inversedBy: 'files')]
    private Collection $subcategories;

    /**
     * @var Collection<int, User>
     */
    #[ORM\JoinTable(name: "file_share")]
    #[JoinColumn(name: 'file_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'friend_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'fileShare')]
    private Collection $share;

    public function __construct()
    {
        $this->subcategories = new ArrayCollection();
        $this->share = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getServerName(): ?string
    {
        return $this->serverName;
    }

    public function setServerName(string $serverName): static
    {
        $this->serverName = $serverName;

        return $this;
    }

    public function getSendingDate(): ?\DateTimeInterface
    {
        return $this->sendingDate;
    }

    public function setSendingDate(\DateTimeInterface $sendingDate): static
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Subcategory>
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Subcategory $subcategory): static
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories->add($subcategory);
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): static
    {
        $this->subcategories->removeElement($subcategory);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getShare(): Collection
    {
        return $this->share;
    }

    public function addShare(User $share): static
    {
        if (!$this->share->contains($share)) {
            $this->share->add($share);
        }

        return $this;
    }

    public function removeShare(User $share): static
    {
        $this->share->removeElement($share);

        return $this;
    }
}
