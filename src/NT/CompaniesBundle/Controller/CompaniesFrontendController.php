<?php

namespace NT\CompaniesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

class CompaniesFrontendController extends Controller
{
    use \NT\FrontendBundle\Traits\NTHelperTrait;

    protected $matcher, $router;
    protected $contentPageId             = 8;
    protected $mainRootName              = 'companies_list';
    protected $companiesCategoriesPerPage = 1000;
    protected $companiesPerPage           = 1000;
    protected $itemsRepo                 = 'NTCompaniesBundle:Company';
    protected $itemsCategoriesRepo       = 'NTCompaniesBundle:CompanyCategory';

    /**
     * @Route("/companies/{page}", name="companies_list", requirements={"page": "\d+"})
     * @Template("NTCompaniesBundle:Frontend:companies_list.html.twig")
     */
    public function companiesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $companiesCategories = $companiesCategoriesRepo->findAllMainCategoriesByLocale($request->getLocale());
        if (count($companiesCategories) > 0) {
            return $this->forward('NTCompaniesBundle:CompaniesFrontend:companiesCategoriesList', array('_route' => 'companies_categories_list', 'page'=> $page));
        }

        $content = $this->getContentPage();

        $query = $companiesRepo->getCompaniesListingQuery(null, $locale, $page, $this->companiesPerPage);
        $companies = new Paginator($query, true);

        $this->generateSeoAndOgTags($content);

        return array(
            'companies'    => $companies,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/companies/{page}", name="companies_categories_list", requirements={"page": "\d+"})
     * @Template("NTCompaniesBundle:Frontend:companies_categories_list.html.twig")
     */
    public function companiesCategoriesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $query = $companiesCategoriesRepo->getCategoriesListingQuery(null, $locale, $page, $this->companiesCategoriesPerPage);
        $companiesCategories = new Paginator($query, true);

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'            => $content,
            'companiesCategories' => $companiesCategories,
            'breadCrumbs'        => $this->generateBreadCrumbs($request),
            'sideBar'            => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/companies/{categorySlug}/{page}", name="companies_categories_category_view", requirements={"page": "\d+"})
     * @Template("NTCompaniesBundle:Frontend:companies_categories_category_view.html.twig")
     */
    public function companiesCategoriesCategoryViewAction(Request $request, $categorySlug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $companyCategory = $companiesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$companyCategory) {
            throw $this->createNotFoundException(sprintf("Company category '%s' not found", $categorySlug));
        }

        $query = $companiesCategoriesRepo->getCategoriesListingQuery($companyCategory->getId(), $locale, $page, $this->companiesCategoriesPerPage);
        $companyCategoryChildren = new Paginator($query, true);

        if ($companyCategoryChildren->count() <= 0) {
            //if no categories go to render companies
            $companiesRepo = $em->getRepository($this->itemsRepo);
            $query = $companiesRepo->getCompaniesListingQuery($companyCategory->getId(), $locale, $page, $this->companiesPerPage);
            $categoryCompanies = new Paginator($query, true);

            $this->generateSeoAndOgTags($companyCategory);

            return $this->render('NTCompaniesBundle:Frontend:companies_category_companies_list.html.twig', array(
                'companyCategory'  => $companyCategory,
                'categoryCompanies' => $categoryCompanies,
                'content'          => $this->getContentPage(),
                'breadCrumbs'      => $this->generateBreadCrumbs($request),
                'sideBar'          => $this->getSideBar($request),
            ));
        }

        $this->generateSeoAndOgTags($companyCategory);

        return array(
            'companyCategory'         => $companyCategory,
            'companyCategoryChildren' => $companyCategoryChildren,
            'content'                 => $this->getContentPage(),
            'breadCrumbs'             => $this->generateBreadCrumbs($request),
            'sideBar'                 => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/companies/{categorySlug}/{slug}", name="companies_category_company_view")
     * @Template("NTCompaniesBundle:Frontend:companies_category_company_view.html.twig")
     */
    public function companiesCategoryCompanyViewAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $company = $companiesRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$company) {
            throw $this->createNotFoundException(sprintf('Company "%s" not found', $slug));
        }

        $companyCategory = $companiesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$companyCategory) {
            throw $this->createNotFoundException(sprintf('Category "%s" not found', $categorySlug));
        }

        $sameCategoryCompanies = $companiesRepo->findSameCategoryCompanies($companyCategory->getId(), $company->getId(), $locale, 3);

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($company);
        $dispatcher->dispatch('nt.seo', $event);

        $params = $this->getImageUrlFromGallery($company->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($company, $params);

        return array(
            'company'              => $company,
            'companyCategory'      => $companyCategory,
            'sameCategoryCompanies' => $sameCategoryCompanies,
            'gallery'              => $this->getGalleryImages($company, $locale),
            'content'              => $this->getContentPage(),
            'breadCrumbs'          => $this->generateBreadCrumbs($request),
            'sideBar'              => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/company/{slug}", name="company_without_category")
     * @Template("NTCompaniesBundle:Frontend:company_without_category.html.twig")
     */
    public function companyWithoutCategoryAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);

        $company = $companiesRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$company) {
            throw $this->createNotFoundException(sprintf('Company "%s" not found', $slug));
        }

        $params = $this->getImageUrlFromGallery($company->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($company, $params);

        return array(
            'company'     => $company,
            'gallery'     => $this->getGalleryImages($company, $locale),
            'content'     => $this->getContentPage(),
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * Render companies on homepage
     * @Template("NTCompaniesBundle:Frontend:homepageCompanies.html.twig")
     */
    public function homepageCompaniesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $companies = $em->getRepository('NTCompaniesBundle:Company')->findAllOnHomepageByLocale($request->getLocale(), 4);

        return array(
            'companies' => $companies
        );
    }

    private function getSideBar(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->itemsCategoriesRepo);
        $locale = $request->getLocale();

        $sideBar = null;
        $menuChildrens = $repo->findAllMainCategoriesByLocale($locale);
        $factory = new MenuFactory();
        $sideBar = $factory->createItem('root', array());

        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $this->createMenu($menuChildrens, $sideBar, $repo, $locale);
        $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());

        return $sideBar != null ? $renderer->render($sideBar, array('currentClass' => 'selected', 'ancestorClass'=>'selected')) : false;
    }

    private function createMenu($children, $menu, $repo, $locale)
    {
        $request = $this->container->get('request');
        $route = $request->get('_route');
        $slug = $request->get('slug');
        $categorySlug = $request->get('categorySlug');
        $requestUri = $request->getRequestUri();

        $params = array();

        foreach ($children as $itm) {
            $uri = $this->generateUrl($itm->getRoute(), $itm->getRouteParams());
            $subMenu = $menu->addChild($itm->getTitle(), array('uri' => $uri, 'currentClass' => 'selected'));
            if ($itm->getSlug() == $slug || $categorySlug == $itm->getSlug()) {
                if ($parentMenu = $subMenu->getParent()) {
                    $parentMenu->setAttribute('class', 'selected');
                }
                $subMenu->setAttribute('class', 'selected');
            }
            if (count($children = $repo->findAllChildrenCategoriesByLocale($itm->getId(), $locale))) {
                $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
                $subMenu->setChildrenAttribute('class', 'dropdown');
                $this->createMenu($children, $subMenu, $repo, $locale);
            }
        }
    }

    private function getGalleryImages($entity, $locale, $first = false)
    {
        if (($gallery = $entity->getTranslations()->get($locale)->getGallery()) != null) {
            $gallery = $this->get('doctrine')->getManager()->getRepository('ApplicationSonataMediaBundle:GalleryHasMedia')
            ->findBy(
                array('gallery' => $gallery->getId()),
                array('position' => 'ASC')
            );
        }
        if ($first && isset($gallery[0])) {
            return $gallery[0];
        }
        return $gallery;
    }
}
