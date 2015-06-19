<?php

namespace CommsyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends Controller
{
    /**
     * @Route("/room/{roomId}/group/{groupId}")
     * @Template()
     */
    public function indexAction($roomId, $groupId, Request $request)
    {   
        return array();
    }

    /**
     * @Route("/room/{roomId}/group")
     * @Template()
     */
    public function listAction($roomId, Request $request)
    {
    	return array(
            'roomId' => $roomId
        );
    }
}
