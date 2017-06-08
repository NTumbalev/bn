<?php

namespace NT\LocationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use NT\LocationsBundle\Entity\Locations;

class LocationsFrontendController extends Controller
{
    /**
     * @Template("NTLocationsBundle:Frontend:homepageLocations.html.twig")
     */
    public function homepageLocationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locations = $em->getRepository('NTLocationsBundle:Location')->findAllByLocale($request->getLocale());

        return array(
            'locations' => $locations
        );
    }
}
