<?php

namespace App\Repository\Post;

use App\Entity\Post\Category;
use App\Entity\Post\Post;
use App\Entity\Post\Tag;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface,
    )
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Customized functions 
     */

        /**
         * Search posts by state from the most recent
         * Option: Category / Tag
         * Pagination: 9 Posts per page
         */
        public function findPublished(
            int $page,
            ?Category $category = null,
            ?Tag $tag = null,
        ): PaginationInterface
        {
            /**
             * Data from published posts in descending order
             */
            $data = $this->createQueryBuilder('posts')
                ->where('posts.state LIKE :state')
                ->setParameter('state','%STATE_PUBLISHED%')
                ->orderBy('posts.createdAt', 'DESC');

            /**
             * If there is a Category variable
             */
            if(isset($category))
            {
                $data = $data
                ->join('posts.categories', 'category')
                ->andWhere(':category IN (category)')
                ->setParameter('category', $category);
            }

            /**
             * If there is a Tag variable
             */
            if(isset($tag))
            {
                $data = $data
                ->join('posts.tags', 'tag')
                ->andWhere(':tag IN (tag)')
                ->setParameter('tag', $tag);
            }

            $data->getQuery()
                ->getResult();

            $posts = $this->paginatorInterface->paginate(
                $data,
                $page,
                9
            );
            return $posts;
        }

    /**
     * Search for posts by Title or Category
     * Pagination: 9 Posts per page
     */
        public function findBySearch(SearchData $searchData): PaginationInterface
        {
            /**
             * Data from published posts in descending order
             */
            $data = $this->createQueryBuilder('posts')
                ->where('posts.state LIKE :state')
                ->setParameter('state', '%STATE_PUBLISHED%')
                ->addOrderBy('posts.createdAt', 'DESC');

            /**
             * If there is data in the variable q of searchData
             * Search by post title
             */
            if (!empty($searchData->q)) {
                $data = $data
                    // ->join('posts.tags', 't')
                    ->andWhere('posts.title LIKE :q')
                    // ->orWhere('t.name LIKE :q')
                    ->setParameter('q', "%{$searchData->q}%");
            }

            /**
             * If there is data in the categories variable of searchData
             * Search by category
             */
            if (!empty($searchData->categories)) {
                $data = $data
                    ->join('posts.categories', 'category')
                    ->andWhere('category.id IN (:categories)')
                    ->setParameter('categories', $searchData->categories);
            }

            $data = $data
                ->getQuery()
                ->getResult();

            $posts = $this->paginatorInterface->paginate(
                $data, 
                $searchData->page, 
                9
            );
            return $posts;
        }

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * Example SQL
     * SELECT * FROM `post` INNER JOIN categories_posts ON post.id = categories_posts.post_id INNER JOIN category ON categories_posts.category_id = category.id WHERE category.name = 'eum 1' ORDER BY post.created_at DESC; 
     */
}
