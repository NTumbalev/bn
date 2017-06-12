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
    protected $companiesPerPage           = 20;
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
     * @Route("/companies/{categorySlug}/{page}", name="companies_list")
     * @Template("NTCompaniesBundle:Frontend:companies_list.html.twig")
     */
    public function companiesListAction(Request $request, $categorySlug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $category = $companiesCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$category) {
            throw $this->createNotFoundException(sprintf("Company category with slug: %s not found!", $categorySlug));
        }

        $location = $request->query->get('location', null);

        $query = $companiesRepo->getCompaniesListingQuery(
            $category->getId(),
            $locale,
            $page,
            $this->companiesPerPage,
            $location
        );

        $companies = new Paginator($query, true);
        // $companies = $companiesRepo->findAllByCategoryAndLocale($category->getId(), $locale);

        $locations = $em->getRepository('NTLocationsBundle:Location')->findAllByLocale($locale);

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'   => $content,
            'category'  => $category,
            'location'  => $location,
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

        $this->addVisit($company);

        return array(
            'company'              => $company,
            'companyCategory'      => $companyCategory,
            'gallery'              => $this->getGalleryImages($company, $locale)
        );
    }

    /**
     * @Route("/company/{slug}", name="company_without_category")
     * @Template("NTCompaniesBundle:Frontend:company_view.html.twig")
     */
    public function companyWithoutCategoryAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $companiesRepo = $em->getRepository($this->itemsRepo);
        $companiesCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $company = $companiesRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$company) {
            throw $this->createNotFoundException(sprintf('Company "%s" not found', $slug));
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($company);
        $dispatcher->dispatch('nt.seo', $event);

        $params = $this->getImageUrlFromGallery($company->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($company, $params);

        $this->addVisit($company);

        return array(
            'company' => $company,
            'gallery' => $this->getGalleryImages($company, $locale)
        );
    }

    /**
     * Render companies on homepage
     * @Template("NTCompaniesBundle:Frontend:homepageCompanyCategories.html.twig")
     */
    public function homepageCompanyCategoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $companyCategories = $em->getRepository('NTCompaniesBundle:CompanyCategory')->findAllOnHomepageByLocale($request->getLocale(), null);

        return array(
            'companyCategories' => $companyCategories
        );
    }

    /**
     * @Route("/publish/company", name="add_company")
     * @Template("NTCompaniesBundle:Frontend:add_company.html.twig")
     */
    public function addCompanyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $settings = $this->get('nt.settings_manager');
        
        $action = array(
            'action' => $this->generateUrl('contacts', array(), true)
        );
        $form = $this->createForm('contacts', null, $action);

        $content = $em->getRepository('NTContentBundle:Content')->findOneById(6);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }


        if ($request->isMethod('POST')) {
            $this->get('session')->getFlashBag()->clear();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $adminMessage = \Swift_Message::newInstance()
                    ->setSubject($translator->trans('publish_company.subject', array(), 'NTFrontendBundle'))
                    ->setFrom($settings->get('sender_email'))
                    ->setTo(explode(',', $settings->get('publish_company_email')))
                    ->setBody(
                        $this->renderView(
                            'NTCompaniesBundle:Email:publish_company_mail.html.twig', array(
                                'data' => $data,
                                'item' => $item,
                            )
                        ),
                        'text/html'
                    )
                ;

                $mailer = $this->get('mailer');
                $mailer->send($adminMessage);

                $this->get('session')->getFlashBag()->add('success', 'Your message has been sent.');
                return $this->redirect($this->generateUrl('contact_success'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Your message has not been sent.');
            }
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('nt.seo', $event);

        $this->get('nt.og_tags')->loadOgTags($content, array());

        return array(
            'form'        => $form->createView(),
            'content'     => $content
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

    private function addVisit(\NT\CompaniesBundle\Entity\Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $company->setVisited($company->getVisited() + 1);
        $em->persist($company);
        $em->flush();
    }
}
