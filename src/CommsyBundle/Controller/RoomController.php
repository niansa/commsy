<?php

namespace CommsyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends Controller
{
    /**
     * @Route("/room/{roomId}")
     * @Template()
     */
    public function homeAction($roomId, Request $request)
    {
        $legacyEnvironment = $this->get('commsy_legacy.environment')->getEnvironment();

        // get room item for information panel
        $roomManager = $legacyEnvironment->getRoomManager();
        $roomItem = $roomManager->getItem($roomId);

        if (!$roomItem) {
            throw $this->createNotFoundException('The requested room does not exist');
        }

        // ...and prepare some data
        $timeSpread = $roomItem->getTimeSpread();
        $numNewEntries = $roomItem->getNewEntries($timeSpread);
        $pageImpressions = $roomItem->getPageImpressions($timeSpread);

        $numActiveMember = $roomItem->getActiveMembers($timeSpread);
        $numTotalMember = $roomItem->getAllUsers();

        $moderators = array();
        $moderatorList = $roomItem->getModeratorList();
        $moderatorUserItem = $moderatorList->getFirst();
        while ($moderatorUserItem) {
            $moderators[] = $moderatorUserItem->getFirstname() . ' ' . $moderatorUserItem->getLastname();

            $moderatorUserItem = $moderatorList->getNext();
        }

        return array(
            'roomItem' => $roomItem,
            'timeSpread' => $timeSpread,
            'numNewEntries' => $numNewEntries,
            'pageImpressions' => $pageImpressions,
            'numActiveMember' => $numActiveMember,
            'numTotalMember' => $numTotalMember,
            'roomModerators' => $moderators,
        );
    }
}
