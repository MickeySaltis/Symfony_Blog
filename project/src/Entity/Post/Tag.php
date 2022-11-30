<?php

namespace App\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use App\Entity\Trait\CategoryTagTrait;
use App\Repository\Post\TagRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('slug', message: 'Ce slug existe déjà.')]
class Tag
{
    /**
     * Parameters
     */

        /**
         * Trait
         */
        use CategoryTagTrait;

        /**
         * Relationship: Many To Many
         */
        #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'tags')]
        #[JoinTable(name: 'tags_posts')]
        private Collection $posts;


    /**
     * Get / Set
     */

        /**
         * Relationship
         */
        public function getPosts(): Collection
        {
            return $this->posts;
        }
        public function addPost(Post $post): self
        {
            if (!$this->posts->contains($post)) {
                $this->posts[] = $post;
            }
            return $this;
        }
        public function removePost(Post $post): self
        {
            $this->posts->removeElement($post);
            return $this;
        }
}
