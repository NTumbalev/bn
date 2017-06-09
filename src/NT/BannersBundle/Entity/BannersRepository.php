<?php
namespace NT\BannersBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BannersRepository extends EntityRepository
{
    use \NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllByPositionAndLocale($position, $locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder()
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.position = :position')
            ->setParameter('locale', $locale)
            ->setParameter('position', $position)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->addOrderBy('c.rank', 'ASC');
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
