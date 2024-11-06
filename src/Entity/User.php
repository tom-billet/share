<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $surname = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'user')]
    private Collection $files;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'ask')]
    private Collection $usersAsk;

    #[ORM\JoinTable(name: "user_ask")]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'ask_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'ask')]
    private Collection $ask;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'userAccept')]
    #[ORM\JoinTable(name: "user_accept",
        joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "accept_id", referencedColumnName:"id")]
    )]
    private Collection $accept;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'accept')]
    private Collection $userAccept;

    /**
     * @var Collection<int, File>
     */
    #[ORM\ManyToMany(targetEntity: File::class, mappedBy: 'share')]
    private Collection $fileShare;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->ask = new ArrayCollection();
        $this->usersAsk = new ArrayCollection();
        $this->accept = new ArrayCollection();
        $this->userAccept = new ArrayCollection();
        $this->fileShare = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getUser() === $this) {
                $file->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAsk(): Collection
    {
        return $this->ask;
    }

    public function addAsk(self $ask): static
    {
        if (!$this->ask->contains($ask)) {
            $this->ask->add($ask);
        }

        return $this;
    }

    public function removeAsk(self $ask): static
    {
        $this->ask->removeElement($ask);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsersAsk(): Collection
    {
        return $this->usersAsk;
    }

    public function addUsersAsk(self $usersAsk): static
    {
        if (!$this->usersAsk->contains($usersAsk)) {
            $this->usersAsk->add($usersAsk);
            $usersAsk->addAsk($this);
        }

        return $this;
    }

    public function removeUsersAsk(self $usersAsk): static
    {
        if ($this->usersAsk->removeElement($usersAsk)) {
            $usersAsk->removeAsk($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAccept(): Collection
    {
        return $this->accept;
    }

    public function addAccept(self $accept): static
    {
        if (!$this->accept->contains($accept)) {
            $this->accept->add($accept);
        }

        return $this;
    }

    public function removeAccept(self $accept): static
    {
        $this->accept->removeElement($accept);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUserAccept(): Collection
    {
        return $this->userAccept;
    }

    public function addUserAccept(self $userAccept): static
    {
        if (!$this->userAccept->contains($userAccept)) {
            $this->userAccept->add($userAccept);
            $userAccept->addAccept($this);
        }

        return $this;
    }

    public function removeUserAccept(self $userAccept): static
    {
        if ($this->userAccept->removeElement($userAccept)) {
            $userAccept->removeAccept($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFileShare(): Collection
    {
        return $this->fileShare;
    }

    public function addFileShare(File $fileShare): static
    {
        if (!$this->fileShare->contains($fileShare)) {
            $this->fileShare->add($fileShare);
            $fileShare->addShare($this);
        }

        return $this;
    }

    public function removeFileShare(File $fileShare): static
    {
        if ($this->fileShare->removeElement($fileShare)) {
            $fileShare->removeShare($this);
        }

        return $this;
    }
}
