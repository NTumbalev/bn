<?php


namespace NT\CompaniesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
*  
*/
class CompaniesBackendController extends \NT\CoreBundle\Controller\SortableCRUDController
{
    public function updateVisitsAction(Request $request)
    {
        if ($request->isMethod("POST")) {
            $postParams = $request->request->all();
            if (isset($postParams['visits']) && ($visits = (int)$postParams['visits']) > 0) {
                $conn = $this->get('database_connection');
                $query = "UPDATE companies as c SET c.visited = $visits";
                $conn->exec($query);

                $session = $this->get('session');
                $session->getFlashBag()->add('success', 'Успешно обновяване на посещенията!');
            }
        }
        return $this->render('NTCompaniesBundle:Backend:updateVisits.html.twig', array(
        ));
    }
}
