<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', 'Cet email est déjà utilisé pour cette application.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Parameters
     */ 
        #[ORM\Id]
        #[ORM\GeneratedValue('CUSTOM')]
        #[ORM\Column(type: 'uuid', unique: true)]
        #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
        private ?string $id = null;

        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank()]
        private string $avatar;

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank()]
        #[Assert\Email()]
        private string $email;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string $lastName;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string $firstName;

        #[ORM\Column(type: 'json')]
        #[Assert\NotBlank()]
        private array $roles = ['ROLE_USER'];

        private ?string $plainPassword = null;

        #[ORM\Column(type: 'string', length: 255)]
        #[Assert\NotBlank()]
        private string $password;

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $createdAt;

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $updatedAt;

    /**
     * Construction: Date
     */
        public function __construct()
        {
            $this->createdAt = new \DateTimeImmutable();
            $this->updatedAt = new \DateTimeImmutable();
        }

    /**
     * Lifecycle: Persist
     */
        #[ORM\PrePersist]
        public function prePersist(): void
        {
            $this->avatar= 'https://avatars.dicebear.com/api/adventurer-neutral/'. $this->email .'.svg';
        }

    /**
     * Lifecycle: Update
     */
        #[ORM\PreUpdate]
        public function preUpdate(): void
        {
            $this->updatedAt = new \DateTimeImmutable();
            $this->avatar= 'https://avatars.dicebear.com/api/adventurer-neutral/'. $this->email .'.svg';
        }

    /**
     * Get / Set
     */ 
        public function getId(): ?string
        {
            return $this->id;
        }


        public function getAvatar(): string
        {
            return $this->avatar;
        }
        // public function setAvatar($avatar): self
        // {
        //     $this->avatar = $avatar;
        //     return $this;
        // }

        public function getEmail(): string
        {
            return $this->email;
        }
        public function setEmail(string $email): self
        {
            $this->email = $email;
            return $this;
        }

        public function getLastName(): string
        {
            return $this->lastName;
        }
        public function setLastName(?string $lastName): self
        {
            $this->lastName = $lastName;
            return $this;
        }

        public function getFirstName(): string
        {
            return $this->firstName;
        }
        public function setFirstName(?string $firstName): self
        {
            $this->firstName = $firstName;
            return $this;
        }

        public function getRoles(): array
        {
            $roles = $this->roles;
            $roles = ['ROLE_USER'];
            return array_unique($roles);
        }
        public function setRoles(array $roles): self
        {
            $this->roles = $roles;
            return $this;
        }

        public function getPlainPassword(): ?string
        {
            return $this->plainPassword;
        }
        public function setPlainPassword(?string $plainPassword): self
        {
            $this->plainPassword = $plainPassword;
            return $this;
        }

        public function getPassword(): string
        {
            return $this->password;
        }
        public function setPassword(string $password): self
        {
            $this->password = $password;
            return $this;
        }

        public function getCreatedAt(): \DateTimeImmutable
        {
            return $this->createdAt;
        }
        public function setCreatedAt(\DateTimeImmutable $createdAt)
        {
            $this->createdAt = $createdAt;
            return $this;
        }

        public function getUpdatedAt(): \DateTimeImmutable
        {
            return $this->updatedAt;
        }
        public function setUpdatedAt(\DateTimeImmutable $updatedAt)
        {
            $this->updatedAt = $updatedAt;
            return $this;
        }

    /**
     * UserInterface
     */
        public function getUserIdentifier(): string
        {
            return $this->email;
        }

        public function eraseCredentials(): void
        {
            $this->plainPassword = null;
        }

}
