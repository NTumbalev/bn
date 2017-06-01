<?php
/**
 * This file is part of the NTCompaniesBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\CompaniesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use NT\PublishWorkflowBundle\PublishWorkflowQueryBuilderTrait;
/**
 * Custom methods for working with Entity
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @package NTCompaniesRepository
 * @author  Nikolay Tumbalev <n.tumbalev@nt.bg>
 */
class CompanyRepository extends EntityRepository
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
     * Find one news by slug and locale
     * @var string $slug
     * @var string $locale
     * @return array
     */
    public function findOneBySlugAndLocale($slug, $locale)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug = :slug')
            ->andWhere('t.title IS NOT NULL')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime());
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Find all by category and locale
     * @var companyCategoryId integer
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findAllByCategoryAndLocale($companyCategoryId, $locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->leftJoin('c.companyCategories', 'cat')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug IS NOT NULL')
            ->andWhere('cat.id = :companyCategoryId')
            ->setParameter('companyCategoryId', $companyCategoryId)
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.rank', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * Query for companies listing
     * @var companyCategoryId integer
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function getCompaniesListingQuery($companyCategoryId, $locale, $page, $pageSize)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder(null);
        $qb
            ->leftJoin('c.translations', 't')
            ->leftJoin('c.companyCategories', 'cat')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug IS NOT NULL');
            if ($companyCategoryId != null) {
                $qb
                ->andWhere('cat.id = :companyCategoryId')
                ->setParameter('companyCategoryId', $companyCategoryId)
                ;
            }
        $qb
            ->setParameter('locale', $locale)
            ->orderBy('c.rank', 'ASC')
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);

        return $qb->getQuery();
    }

    /**
     * Find all by category and locale
     * @var companyCategoryId integer
     * @var companyId integer
     * @var locale string
     * @var limit integer
     * @var offset integer
     * @return array
     */
    public function findSameCategoryCompanies($companyCategoryId, $companyId, $locale, $limit = null, $offset = null)
    {
        $qb = $this->getPublishWorkFlowQueryBuilder();
        $qb
            ->leftJoin('c.translations', 't')
            ->leftJoin('c.companyCategories', 'cat')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.slug IS NOT NULL')
            ->andWhere('cat.id = :companyCategoryId')
            ->andWhere('c.id != :companyId')
            ->setParameter('companyCategoryId', $companyCategoryId)
            ->setParameter('companyId', $companyId)
            ->setParameter('locale', $locale)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.rank', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $qb->getQuery();

        return $query->getResult();
    }
}
