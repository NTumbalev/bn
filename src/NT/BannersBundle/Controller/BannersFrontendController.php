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
    public function renderBannersAction(
        Request $request,
        $position,
        $random,
        $pageId = null,
        $locationId = null,
        $offset = null,
        $limit = null
    )
    {
        return array(
            'banners' => $this->getBanners(
                $request->getLocale(),
                $position,
                $random,
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

        return array(
            'banners' => $this->getRandomBanner($banners),
        );
    }

    private function getBanners(
        $locale,
        $position,
        $random,
        $pageId = null,
        $locationId = null,
        $offset = null,
        $limit = null
    )
    {
        $em = $this->getDoctrine()->getManager();
        $banners = $em->getRepository('NTBannersBundle:BannersPages')->findAllBannersByCriteria(
            $locale,
            $position,
            $pageId,
            $locationId,
            $offset,
            $limit
        );

        if ($random === true) {
            $banners = $this->getRandomBanner($banners);
        }

        return $banners;
    }

    private function getRandomBanner($banners)
    {
        $number = rand(0, count($banners) - 1);
        return count($banners) > 0 ? [$banners[$number]] : [];
    }
}
