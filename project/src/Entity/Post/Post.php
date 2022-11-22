<?php

namespace App\Entity\Post;

use App\Repository\Post\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Cocur\Slugify\Slugify;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug', message: 'Ce slug existe déjà.')]
class Post
{
    /**
     * Table of states
     */
    const STATES = ['STATE_DRAFT', 'STATE_PUBLISHED'];

    /**
     * Parameters
     */
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank()]
        private string $title;

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank()]
        private string $slug;

        #[ORM\Column(type: 'text', length: 255)]
        #[Assert\NotBlank()]
        private string $content;

        #[ORM\Column(type: 'string', length: 255)]
        private string $state = Post::STATES[0];

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $updatedAt;

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $createdAt;

        /**
         * Relationship: One To One
         */
        #[ORM\OneToOne(inversedBy: 'post', targetEntity: Thumbnail::class, cascade: ['persist', 'remove'])]
        private ?Thumbnail $thumbnail = null;

        /**
         * Construction: Date
         */
        public function __construct()
        {
            $this->updatedAt = new \DateTimeImmutable();
            $this->createdAt = new \DateTimeImmutable();
        }

        /**
         * Lifecycle: Persist
         */
        #[ORM\PrePersist] 
        public function prePersist()
        {
            $this->slug = (new Slugify())->slugify($this->title);
        }

        /**
         * Lifecycle: Update
         */
        #[ORM\PreUpdate]
        public function preUpdate()
        {
            $this->updatedAt = new \DateTimeImmutable();
        }
        

    /**
     * Get / Set
     */
        public function getId(): ?int
        {
            return $this->id;
        }

        public function getTitle(): ?string
        {
            return $this->title;
        }
        public function setTitle(string $title): self
        {
            $this->title = $title;
            return $this;
        }

        public function getSlug(): ?string
        {
            return $this->slug;
        }
        public function setSlug(string $slug): self
        {
            $this->slug = $slug;
            return $this;
        }

        public function getContent(): ?string
        {
            return $this->content;
        }
        public function setContent(string $content): self
        {
            $this->slug = $content;
            return $this;
        }

        public function getState(): ?string
        {
            return $this->state;
        }
        public function setState(string $state): self
        {
            $this->state = $state;
            return $this;
        }

        public function getUpdatedAt(): \DateTimeImmutable
        {
            return $this->updatedAt;
        }
        public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
        {
            $this->updatedAt = $updatedAt;
            return $this;
        }

        public function getCreatedAt(): \DateTimeImmutable
        {
            return $this->createdAt;
        }
        public function setCreatedAt(\DateTimeImmutable $createdAt): self
        {
            $this->createdAt = $createdAt;
            return $this;
        }

        public function getThumbnail(): ?Thumbnail
        {
            return $this->thumbnail;
        }
        public function setThumbnail(?Thumbnail $thumbnail): self
        {
            $this->thumbnail = $thumbnail;
            return $this;
        }


    /**
     * Format: String
     */
    public function __toString()
    {
        return $this->title;
    }
}