<?php

namespace App\Entity\Post;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use App\Repository\Post\PostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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

        #[ORM\Column(type: 'text')]
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
         * Relationship: Many To Many
         */
        #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'posts')]
        private Collection $categories;

        #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'posts')]
        private Collection $tags;

        #[ORM\ManyToMany(targetEntity: User::class)]
        #[JoinTable('user_post_like')]
        private Collection $likes;

        /**
         * Relationship: Many To Many
         */
        #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
        private Collection $comments;

        /**
         * Construction: Date & Relationship
         */
        public function __construct()
        {
            $this->updatedAt = new \DateTimeImmutable();
            $this->createdAt = new \DateTimeImmutable();
            $this->categories = new ArrayCollection();
            $this->tags = new ArrayCollection();
            $this->likes = new ArrayCollection();
            $this->comments = new ArrayCollection();
        }

        /**
         * Lifecycle: Persist
         */
        #[ORM\PrePersist] 
        public function prePersist(): void
        {
            $this->slug = (new Slugify())->slugify($this->title);
        }

        /**
         * Lifecycle: Update
         */
        #[ORM\PreUpdate]
        public function preUpdate(): void
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
            $this->content = $content;
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
         * Relationship
         */

            /**
             * Category
            */
            public function getCategories(): Collection
            {
                return $this->categories;
            }
            public function addCategory(Category $category): self
            {
                if(!$this->categories->contains($category))
                {
                    $this->categories[] = $category;
                    $category->addPost($this);
                }
                return $this;
            }
            public function removeCategory(Category $category): self
            {
                if(!$this->categories->contains($category))
                {
                    $category->removePost($this);
                }
                return $this;
            }

            /**
             * Tag
             */
            public function getTags(): Collection
            {
                return $this->tags;
            }
            public function addTag(Tag $tag): self
            {
                if(!$this->tags->contains($tag))
                {
                    $this->tags[] = $tag;
                    $tag->addPost($this);
                }
                return $this;
            }
            public function removeTag(Tag $tag): self
            {
                if(!$this->tags->contains($tag))
                {
                    $tag->removePost($this);
                }
                return $this;
            }

            /**
             * Likes
             */
            public function getLikes(): Collection
            {
                return $this->likes;
            }
            public function addLike(User $like): self
            {
                if(!$this->likes->contains($like))
                {
                    $this->likes[] = $like;
                }
                return $this;
            }
            public function removeLike(User $like): self
            {
                $this->likes->removeElement($like);
                return $this;
            }
            public function isLikedByUser(User $user): bool
            {
                return $this->likes->contains($user);
            }
            public function howManyLikes(): int
            {
                return count($this->likes);
            }

            /**
             * Comments
             */
            public function getComments(): Collection
            {
                return $this->comments;
            }
            public function addComment(Comment $comment): self
            {
                if(!$this->comments->contains($comment))
                {
                    $this->comments[] = $comment;
                    $comment->setPost($this);
                }
                return $this;
            }
            public function removeComment(Comment $comment): self
            {
                if($this->comments->removeElement($comment))
                {
                    if($comment->getPost() === $this)
                    {
                        $comment->setPost(null);
                    }
                }
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
