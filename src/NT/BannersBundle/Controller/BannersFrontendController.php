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
    public function renderBannersAction(Request $request, $position, $isMain, $pageId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $banners = $em->getRepository('NTBannersBundle:BannersPages')->findAllBannersByCriteria(
            $request->getLocale(), 
            $position,
            $isMain, 
            $pageId
        );

        return array(
            'banners' => $banners
        );
    }
}
