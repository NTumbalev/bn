<?php

namespace NT\AccentsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use NT\AccentsBundle\Entity\Accents;

class AccentsFrontendController extends Controller
{
    /**
     * @Template("NTAccentsBundle:Frontend:homepageAccents.html.twig")
     */
    public function homepageAccentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accents = $em->getRepository('NTAccentsBundle:Accent')->findAllByLocale($request->getLocale());

        return array(
            'accents' => $accents
        );
    }
}
