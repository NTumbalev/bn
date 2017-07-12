<?php

namespace NT\BannersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class BannersFrontendController extends Controller
{
    /**
     * @Template("NTBannersBundle:Frontend:renderBanners.html.twig")
     */
    public function renderBannersAction(Request $request, $position, $isMain, $pageId = null, $locationId = null, $offset = null, $limit = null)
    {
        return array(
            'banners' => $this->getBanners(
                $request->getLocale(),
                $position,
                $isMain,
                $pageId,
                $locationId,
                $offset,
                $limit
            )
        );
    }

    /**
     * @Template("NTBannersBundle:Frontend:renderBanners.html.twig")
     */
    public function renderHomepageBannerAction(Request $request)
    {
        $banners = $this->getBanners($request->getLocale(), 'homepage', false);

        $number = rand(0, count($banners) - 1);

        return array(
            'banners' => [count($banners) > 0 ? $banners[$number] : []],
        );
    }

    private function getBanners(
        $locale,
        $position, 
        $isMain, 
        $pageId = null, 
        $locationId = null, 
        $offset = null, 
        $limit = null
    )
    {
        $em = $this->getDoctrine()->getManager();
        
        return $em->getRepository('NTBannersBundle:BannersPages')->findAllBannersByCriteria(
            $locale,
            $position,
            $isMain,
            $pageId,
            $locationId,
            $offset,
            $limit
        );   
    }
}
