<?php

namespace App\Entity\Post;

use App\Entity\User;
use App\Entity\Post\Post;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:CommentRepository::class)]
class Comment
{
    /**
     * Parameters
     */ 

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(type: 'text')]
        #[Assert\NotBlank()]
        private string $content;

        #[ORM\Column(type: 'boolean')]
        private bool $isApproved = false;

        #[ORM\Column(type: 'datetime_immutable')]
        #[Assert\NotNull()]
        private \DateTimeImmutable $createdAt;

    /**
     * Relationship: Many To Many
     */
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private User $author;

        #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
        #[ORM\JoinColumn(nullable: false)]
        private Post $post;


    /**
     * Construction: Date & Relationship
     */
        public function __construct()
        {
            $this->createdAt = new \DateTimeImmutable();
        }

    /**
     * Get / Set
     */

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getContent(): ?string
        {
            return $this->content;
        }
        public function setContent(string $content): self
        {
            $this->content = $content;
            return $this;
        }

        public function isIsApproved(): ?bool
        {
            return $this->isApproved;
        }
        public function setIsApproved(bool $isApproved): self
        {
            $this->isApproved = $isApproved;
            return $this;
        }

        public function getAuthor(): ?User
        {
            return $this->author;
        }
        public function setAuthor(?User $author): self
        {
            $this->author = $author;
            return $this;
        }

        public function getPost(): ?Post
        {
            return $this->post;
        }
        public function setPost(?Post $post): self
        {
            $this->post = $post;
            return $this;
        }

        public function getCreatedAt(): ?\DateTimeImmutable
        {
            return $this->createdAt;
        }
        public function setCreatedAt(\DateTimeImmutable $createdAt): self
        {
            $this->createdAt = $createdAt;
            return $this;
        }
}