<?php

namespace NT\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMapping;

class SearchFrontendController extends Controller
{
    /**
     * Route("/search", name="search")
     * @Template("NTSearchBundle:Frontend:search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();

        $locations = $em->getRepository('NTLocationsBundle:Location')->findAllByLocale($locale);
        $masterRoute = $this->container->get('request_stack')->getMasterRequest()->get("_route");

        return array(
            'masterRoute' => $masterRoute,
            'locations' => $locations
        );
    }
    
    /**
     * @Route("/search", name="search")
     * @Template("NTSearchBundle:Frontend:search_result.html.twig")
     */
    public function searchResultAction(Request $request)
    {
        $search = $request->query->get('search');
        $location = $request->query->get('location');
        
        $locale = $request->getLocale();

        $em = $this->getDoctrine()->getManager();
        $contentObj = $em->getRepository('NTContentBundle:Content')->findOneById(7);
        if (!$contentObj) {
            throw $this->createNotFoundException();
        }

        $contentPages = array();

        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('NTContentBundle', $bundles)) {
            $contentPages = $this->search($em, 'NTContentBundle:Content', 'NTContentBundle:ContentTranslation', $search, $locale);
        }

        // if (array_key_exists('NTCompaniesBundle', $bundles)) {
        //     $companyCategories = $this->search($em, 'NTCompaniesBundle:CompanyCategory', 'NTCompaniesBundle:CompanyCategoryTranslation', $search, $locale, $location);
        // }
        
        $companies = $em->getRepository('NTCompaniesBundle:Company')->doSearch($search, $location);
        $companyCategories = $em->getRepository('NTCompaniesBundle:CompanyCategory')->doSearch($search, $location);

        $results = array_merge_recursive(
            $contentPages,
            $companies,
            $companyCategories
        );

        $totalResults = count($results);
        $breadCrumbs = array($contentObj->getTitle() => null);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($contentObj);
        $dispatcher->dispatch('nt.seo', $event);

        $locations = $em->getRepository('NTLocationsBundle:Location')->findAllByLocale($locale);

        return array(
            'companies'         => $companies,
            'companyCategories' => $companyCategories,
            'contentPages'      => $contentPages,
            'content'           => $contentObj,
            'search'            => $search,
            'location'          => $location,
            'totalResults'      => $totalResults,
            'breadCrumbs'       => $breadCrumbs,
        );
    }

    private function search($em, $entityClass, $i18nClass, $search, $locale, $locations = null)
    {
        $search = mb_strtolower($search, "UTF-8");
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult($entityClass, 't');
        $rsm->addFieldResult('t', 'id', 'id');
        $rsm->addFieldResult('t', 'lvl', 'lvl');
        $rsm->addFieldResult('t', 'isSystem', 'isSystem');
        $rsm->addJoinedEntityResult($i18nClass, 'i18n', 't', 'translations');
        $rsm->addFieldResult('i18n', 'i_id', 'id');
        $rsm->addFieldResult('i18n', 'slug', 'slug');
        $rsm->addFieldResult('i18n', 'title', 'title');
        $rsm->addFieldResult('i18n', 'description', 'description');
        $rsm->addJoinedEntityResult('NTPublishWorkflowBundle:PublishWorkflow', 'pw', 't', 'publishWorkflow');
        $rsm->addFieldResult('pw', 'pw_id', 'id');
        $rsm->addFieldResult('pw', 'is_active', 'isActive');
        $rsm->addFieldResult('pw', 'from_date', 'fromDate');
        $rsm->addFieldResult('pw', 'to_date', 'toDate');

        $tableName = $em->getClassMetadata($entityClass)->getTableName();
        $tableI18nName = $em->getClassMetadata($i18nClass)->getTableName();

        if ($entityClass == 'NTContentBundle:Content') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, t.is_system
                FROM {$tableName} t left join {$tableI18nName} i18n on t.id=i18n.object_id
                LEFT JOIN publish_workflow pw ON pw.id = t.publishWorkflow_id
                WHERE
                i18n.locale = ?
                AND
                (
                    LOWER(i18n.title) REGEXP ?
                    OR
                    LOWER(i18n.description) REGEXP ?
                )
                AND pw.is_active = 1
                AND
                (
                    (pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())
                    OR
                    (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW())
                )
                ", $rsm
            )
            ->setParameter(1, $locale)
            ->setParameter(2, '[[:<:]]'.$search.'[[:>:]]')
            ->setParameter(3, '[[:<:]]'.$search.'[[:>:]]');
        } elseif ($entityClass == 'NTCompaniesBundle:CompanyCategoryy') {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title, pc.companycategory_id
                FROM {$tableName} t
                LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
                LEFT JOIN company_categories pc on t.id=pc.product_id
                LEFT JOIN publish_workflow pw ON pw.id = t.publishWorkflow_id
                WHERE
                i18n.locale = ?
                AND
                (
                    LOWER(i18n.title) REGEXP ?
                    OR
                    LOWER(i18n.description) REGEXP ?
                )
                AND pw.is_active = 1
                AND
                (
                    (pw.from_date IS NULL AND pw.to_date IS NULL) OR (pw.from_date <= NOW() AND pw.to_date >= NOW())
                    OR
                    (pw.from_date IS NOT NULL AND pw.from_date <= NOW()) OR (pw.to_date IS NOT NULL AND pw.to_date >= NOW())
                )
                ", $rsm
            )
            ->setParameter(1, $locale)
            ->setParameter(2, '[[:<:]]'.$search.'[[:>:]]')
            ->setParameter(3, '[[:<:]]'.$search.'[[:>:]]');
        } else {
            $query = $em->createNativeQuery(
                "SELECT t.id, i18n.id as i_id, i18n.slug, i18n.title
                FROM {$tableName} t 
                LEFT JOIN {$tableI18nName} i18n on t.id=i18n.object_id
                WHERE
                i18n.locale = ?
                AND
                (
                    LOWER(i18n.title) REGEXP ?
                )
                ", $rsm
            )
            ->setParameter(1, $locale)
            ->setParameter(2, '[[:<:]]'.$search.'[[:>:]]');
        }

        return $query->getResult();
    }
}
