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

    protected $matcher;
    protected $router;
    protected $contentPageId              = 2;
    protected $mainRootName               = 'categories_list';
    protected $itemsRepo                  = 'NTCompaniesBundle:Company';
    protected $itemsCategoriesRepo        = 'NTCompaniesBundle:CompanyCategory';

    /**
     * @Route("/categories", name="categories_list")
     * @Template("NTCompaniesBundle:Frontend:categories_list.html.twig")
     */
    public function categoriesListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $companiesCategories = $companiesCategoriesRepo->findAllByLocale($locale);

        $categories = array();
        foreach ($companiesCategories as $category) {
            $firstCapitalLetter = mb_strtoupper(mb_strcut($category->getTitle(), 1, 2));
            $categories[$firstCapitalLetter][] = $category;
        }

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'             => $content,
            'companiesCategories' => $categories,
        );
    }

    /**
     * @Route("/companies/{categorySlug}", name="companies_list")
     * @Template("NTCompaniesBundle:Frontend:companies_list.html.twig")
     */
    public function companiesListAction(Request $request, $categorySlug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $category = $companiesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$category) {
            throw $this->createNotFoundException(sprintf("Company category with slug: %s not found!", $categorySlug));
        }

        $companies = $companiesRepo->findAllByCategoryAndLocale($category->getId(), $locale);
        $locations = $em->getRepository('NTLocationsBundle:Location')->findAllByLocale($locale);

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'   => $content,
            'category'  => $category,
            'locations' => $locations,
            'companies' => $companies,
        );
    }

    /**
     * @Route("/company/{categorySlug}/{companySlug}", name="company_view")
     * @Template("NTCompaniesBundle:Frontend:company_view.html.twig")
     */
    public function companyViewAction(Request $request, $categorySlug, $companySlug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $company = $companiesRepo->findOneBySlugAndLocale($companySlug, $locale);
        if (!$company) {
            throw $this->createNotFoundException(sprintf('Company "%s" not found', $companySlug));
        }

        $companyCategory = $companiesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$companyCategory) {
            throw $this->createNotFoundException(sprintf('Category "%s" not found', $categorySlug));
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($company);
        $dispatcher->dispatch('nt.seo', $event);

        $params = $this->getImageUrlFromGallery($company->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($company, $params);

        return array(
            'company'              => $company,
            'companyCategory'      => $companyCategory,
            'gallery'              => $this->getGalleryImages($company, $locale)
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

        $companies = $em->getRepository('NTCompaniesBundle:CompanyCategory')->findAllOnHomepageByLocale($request->getLocale(), null);

        return array(
            'companies' => $companies
        );
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
