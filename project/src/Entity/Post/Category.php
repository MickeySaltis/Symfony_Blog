<?php

namespace App\Entity\Post;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use App\Repository\Post\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug', message: 'Ce slug existe déjà.')]
class Category
{
    /**
     * Parameters
     */
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private ?int $id = null;

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank()]
        private string $name;

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank()]
        private string $slug ='';

        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $descriptions = null;

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $createdAt;

        /**
         * Relationship: Many To Many
         */
        #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'categories')]
        #[JoinTable(name: 'categories_posts')]
        private Collection $posts;

        /**
         * Construction: Date & Relationship
         */
        public function __construct()
        {
            $this->createdAt = new \DateTimeImmutable();
            $this->posts = new ArrayCollection();
        }

        /**
         * Lifecycle: Persist
         */
        #[ORM\PrePersist]
        public function prePersist()
        {
            $this->slug = (new Slugify())->slugify($this->name);
        }

    /**
     * Get / Set
     */    
        public function getId(): ?int
        {
            return $this->id;
        }

        public function getName(): string
        {
            return $this->name;
        }
        public function setName(string $name): self
        {
            $this->name = $name;
            return $this;
        }
 
        public function getSlug(): string
        {
            return $this->slug;
        }
        public function setSlug(string $slug): self
        {
            $this->slug = $slug;
            return $this;
        }

        public function getDescriptions(): ?string
        {
            return $this->descriptions;
        }
        public function setDescriptions(?string $descriptions): self
        {
            $this->descriptions = $descriptions;
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

        /**
         * Relationship
         */
        public function getPosts(): Collection
        {
            return $this->posts;
        }
        public function addPost(Post $post): self
        {
            if(!$this->posts->contains($post))
            {
                $this->posts[] = $post;
            }
            return $this;
        }
        public function removePost(Post $post): self
        {
            $this->posts->removeElement($post);
            return $this;
        }

    /**
     * Format: String
     */
    public function __toString()
    {
        return $this->name;
    }
}
