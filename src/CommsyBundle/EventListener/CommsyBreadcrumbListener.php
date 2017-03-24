<?php

namespace CommsyBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

use Commsy\LegacyBundle\Services\LegacyEnvironment;
use Commsy\LegacyBundle\Utils\RoomService;
use Commsy\LegacyBundle\Utils\ItemService;

class CommsyBreadcrumbListener
{
    private $legacyEnvironment;
    private $roomService;
    private $itemService;
    private $translator;
    private $breadcrumbs;
    private $router;

    public function __construct(LegacyEnvironment $legacyEnvironment, RoomService $roomService, ItemService $itemService, TranslatorInterface $translator, Router $router, Breadcrumbs $whiteOctoberBreadcrumbs)
    {
        $this->legacyEnvironment = $legacyEnvironment;
        $this->roomService = $roomService;
        $this->itemService = $itemService;
        $this->breadcrumbs = $whiteOctoberBreadcrumbs;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $request = $event->getRequest();

        $route = explode('_', $request->get('_route'));

        if (count($route) < 3) {
            return;
        }

        list($bundle, $controller, $action) = $route;

        $routeParameters = $request->get('_route_params');

        $roomItem = $this->roomService->getCurrentRoomItem();

        // Portal / CommunityRoom / ProjectRooms / ProjectRoomName / ProjectRoom / Groups / GroupName / Grouproom / Rubric / Entry

        $this->addPortalCrumb($request);

        if ($roomItem->isGroupRoom()) {
            dump("GroupRoom");
            $projectRoomItem = $roomItem->getLinkedProjectItem();

            $this->addCommunityCrumbs($projectRoomItem);

            $this->addRoomCrumb($projectRoomItem, true);
            // TODO: add linked breadcrumbs to:
            //      - group rubric of containing project room
            $this->breadcrumbs->addItem($this->translator->trans('groups', [], 'menu'));
        }
        elseif ($roomItem->isProjectRoom()) {
            dump("ProjectRoom");
            $this->addCommunityCrumbs($roomItem);
        }
        elseif ($roomItem->isCommunityRoom()) {
            dump("CommunityRoom");
            # code...
        }

        if($controller == 'profile'){
            $this->addProfileCrumbs($roomItem, $action);
        }
        elseif ($controller == 'room' && $action == 'home') {
            $this->addRoomCrumb($roomItem, false);
        }
        else {
            $this->addRoomCrumb($roomItem, true);
            // rubric & entry
            if(array_key_exists('itemId', $routeParameters)) {
                $route[2] = 'list';
                $this->breadcrumbs->addRouteItem($this->translator->trans($controller, [], 'menu'), implode("_", $route), $routeParameters);
                $item = $this->itemService->getTypedItem($request->get('itemId'));
                $this->breadcrumbs->addItem($item->getTitle());
            }
            else {
                $this->breadcrumbs->addItem($this->translator->trans($controller, [], 'menu'));
            }
        }
    }

    private function addPortalCrumb($request)
    {
        $portal = $this->legacyEnvironment->getEnvironment()->getCurrentPortalItem();
        if ($portal) {
            $this->breadcrumbs->prependItem($portal->getTitle(), $request->getSchemeAndHttpHost() . '?cid=' . $portal->getItemId());
        }
    }

    private function addCommunityCrumbs($roomItem)
    {
        dump($roomItem);
        $communityRoomItem = $roomItem->getCommunityList()->getFirst();
        if ($communityRoomItem) {
            $this->addRoomCrumb($communityRoomItem, true);
            $this->breadcrumbs->addRouteItem($this->translator->trans('project', [], 'menu'), "commsy_project_list", array('roomId' => $communityRoomItem->getItemId()));
        }
        else {
            dump("No community room item found!");
        }
    }

    private function addRoomCrumb($roomItem, $asLink)
    {
        $crumbText = $roomItem->isGroupRoom() ? $this->translator->trans('grouproom', [], 'group') : $roomItem->getTitle();
        if ($asLink == true) {
            $this->breadcrumbs->addRouteItem($crumbText, "commsy_room_home", [
                'roomId' => $roomItem->getItemID(),
            ]);
        }
        else {
            $this->breadcrumbs->addItem($crumbText);
        }
    }

    private function addProfileCrumbs($roomItem, $action)
    {
        if($action == 'account') {

        }
        elseif ($action == 'general') {

        }
    }

    private function addRubricCrumb($route, $asLink)
    {
        if($asLink) {
            $this->breadcrumbs->addRouteItem($this->translator->trans($controller, [], 'menu'), implode("_", $route), $routeParameters);
        }
        else {
            $this->breadcrumbs->addItem($this->translator->trans($controller, [], 'menu'));
        }
    }
}