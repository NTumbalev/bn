<?php
/**
 * This file is part of the NTCustomBlocksBundle.
 *
 * (c) Nikolay Tumbalev <ntumbalev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\CustomBlocksBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;

/**
 *  Repository class for CustomBlocks
 *
 * @package NTCustomBlocksBundle
 * @author  Nikolay Tumbalev <ntumbalev@gmail.com>
 */
class CustomBlockRepository extends EntityRepository
{
    use PublishWorkflowQueryBuilderTrait;

    /**
     * Find all by locale
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllByLocale($locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Find one by slug and locale
     * @var slug string
     * @var locale string
     * @return \NT\ContentBundle\Entity\Content|null
     */
    public function findOneByIdAndLocale($id, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('c.id = :id')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('locale', $locale)
            ->setParameter('id', $id)
            ->setParameter('now', new \DateTime());
        $query = $qb->getQuery();
        $results = $query->getResult();

        if (count($results)) {
            return $results[0];
        }

        return;
    }
}
