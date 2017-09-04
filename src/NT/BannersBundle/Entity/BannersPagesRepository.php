<?php
namespace NT\BannersBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BannersPagesRepository extends EntityRepository
{
    use \NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllBannersByCriteria(
        $locale, 
        $position,  
        $pageId, 
        $locationId,
        $onAllCategories = false, 
        $offset = null, 
        $limit = null
    )
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.banner', 'b')
            ->leftJoin('b.translations', 't')
            ->leftJoin('b.publishWorkflow', 'w')
            ->where('
                w.isActive = 1 AND
                (
                    (w.fromDate IS NULL AND w.toDate IS NULL) OR
                    (w.fromDate <= :now AND w.toDate >= :now) OR
                    (
                        (
                            (w.fromDate IS NOT NULL AND w.fromDate <= :now) AND
                            ((w.toDate >= :now OR w.toDate IS NULL) OR (w.toDate IS NOT NULL AND w.toDate >= :now))
                        )
                    )
                )
            ')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.position = :position');

            if ($pageId != null && $onAllCategories === false) {
                $qb
                ->andWhere('c.page = :pageId')
                ->setParameter('pageId', $pageId);
            }

            if ($locationId != null) {
                $qb
                ->andWhere('b.location = :locationId')
                ->setParameter('locationId', $locationId);
            }

            $qb
            ->setParameter('now', new \DateTime())
            ->setParameter('locale', $locale)
            ->setParameter('position', $position)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.rank', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
