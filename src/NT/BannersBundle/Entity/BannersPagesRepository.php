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
    public function findAllBannersByCriteria($locale, $position, $isMain, $pageId, $limit = null, $offset = null)
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
            
            if ($pageId != null) {
                $qb
                ->andWhere('c.page = :pageId')
                ->setParameter('pageId', $pageId);
            } else {
                $qb
                ->andWhere('c.isMain = :isMain')
                ->setParameter('isMain', $isMain)
                ;
            }

            $qb
            ->setParameter('now', new \DateTime())
            ->setParameter('locale', $locale)
            ->setParameter('position', $position)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.rank', 'ASC');
        $query = $qb->getQuery();
        // echo "<pre>"; var_dump($query); echo "</pre>"; exit;

        return $query->getResult();
    }
}
