<?php

namespace App\Controller;

use App\Form\Type\Room\DeleteType;
use App\Form\Type\Room\SecureDeleteType;
use App\Services\LegacyEnvironment;
use App\Utils\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SecureProjectDetailDeletionController
 * @package App\Controller
 * @Security("is_granted('ITEM_ENTER', roomId)")
 */
class SecureProjectDetailDeletionController extends AbstractController
{

    /**
     * @Route("/room/{roomId}/settings/securedelete/{communityId?}")
     * @Template
     * @Security("is_granted('MODERATOR') or is_granted('PARENT_MODERATOR', communityId)")
     */
    public function deleteOrLock(
        $roomId,
        Request $request,
        RoomService $roomService,
        TranslatorInterface $translator,
        LegacyEnvironment $legacyEnvironment
    )
    {
        $roomItem = $roomService->getRoomItem($roomId);
        if (!$roomItem) {
            throw $this->createNotFoundException('No room found for id ' . $roomId);
        }

        $relatedGroupRooms = [];
        if ($roomItem instanceof \cs_project_item) {
            $relatedGroupRooms = $roomItem->getGroupRoomList()->to_array();
        }

        $form = $this->createForm(SecureDeleteType::class, $roomItem, [
            'confirm_string' => $translator->trans('delete', [], 'profile')
        ]);

        $lockForm = $this->createForm(SecureDeleteType::class, $roomItem, [
            'confirm_string' => $translator->trans('lock', [], 'profile')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('delete')->isClicked()) {
                $roomItem->delete();
                $roomItem->save();

                // redirect back to portal
                $portal = $legacyEnvironment->getEnvironment()->getCurrentPortalItem();
                $url = $request->getSchemeAndHttpHost() . '?cid=' . $portal->getItemId();

                return $this->redirect($url);
            }else{
                $form->clearErrors(true);
            }
        }

        $lockForm->handleRequest($request);
        if ($lockForm->isSubmitted() && $form->isValid()) {
            if ($lockForm->get('lock')->isClicked()) {
                $roomItem->reject();
                $roomItem->save();

                // redirect back to portal
                $portal = $legacyEnvironment->getEnvironment()->getCurrentPortalItem();
                $url = $request->getSchemeAndHttpHost() . '?cid=' . $portal->getItemId();

                return $this->redirect($url);
            }else{
                $lockForm->clearErrors(true);
            }
        }

        if ($lockForm->get('lock')->isClicked()) {
            $form = $this->createForm(DeleteType::class, $roomItem, [
                'confirm_string' => $translator->trans('delete', [], 'profile')
            ]);
        }elseif($form->get('delete')->isClicked()){
            $lockForm = $this->createForm(DeleteType::class, $roomItem, [
                'confirm_string' => $translator->trans('lock', [], 'profile')
            ]);
        }

        return [
            'form' => $form->createView(),
            'relatedGroupRooms' => $relatedGroupRooms,
            'lock_form' => $lockForm->createView(),
        ];

    }
}
