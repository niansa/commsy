<?php
namespace Commsy\LegacyBundle\Form\DataTransformer;

use Commsy\LegacyBundle\Utils\RoomService;
use Commsy\LegacyBundle\Utils\UserService;
use Commsy\LegacyBundle\Services\LegacyEnvironment;
use Commsy\LegacyBundle\Form\DataTransformer\DataTransformerInterface;

class AdditionalSettingsTransformer implements DataTransformerInterface
{
    private $legacyEnvironment;

    public function __construct(LegacyEnvironment $legacyEnvironment, RoomService $roomService, UserService $userService)
    {
        $this->legacyEnvironment = $legacyEnvironment->getEnvironment();
        $this->roomService = $roomService;
    }

    /**
     * Transforms a cs_room_item object to an array
     *
     * @param cs_room_item $roomItem
     * @return array
     */
    public function transform($roomItem)
    {
        $roomData = array();

        $defaultRubrics = $roomItem->getAvailableDefaultRubricArray();

        if ($roomItem) {
            $roomData['structural_auxilaries']['buzzwords']['activate'] = $roomItem->withBuzzwords();
            $roomData['structural_auxilaries']['buzzwords']['mandatory'] = $roomItem->isBuzzwordMandatory();

            $roomData['structural_auxilaries']['categories']['activate'] = $roomItem->withTags();
            $roomData['structural_auxilaries']['categories']['mandatory'] = $roomItem->isTagMandatory();
            $roomData['structural_auxilaries']['categories']['edit'] = $roomItem->isTagEditedByAll();

            $roomData['template']['status'] = $roomItem->isTemplate();
            if($roomItem->isCommunityRoom()){
                $roomData['template']['template_availability'] = $roomItem->getCommunityTemplateAvailability();
            }
            else{
                $roomData['template']['template_availability'] = $roomItem->getTemplateAvailability();
            }
            //$roomData['room_status'] = !$roomItem->isOpen();

            dump($roomData);
        }
        return $roomData;
    }

    /**
     * Save additional settings
     *
     * @param object $roomObject
     * @param array $roomData
     * @return cs_room_item|null
     * @throws TransformationFailedException if room item is not found.
     */
    public function applyTransformation($roomObject, $roomData)
    {
        /********* save buzzword and tag options ******/
        $buzzwords = $roomData['structural_auxilaries']['buzzwords'];
        $categories = $roomData['structural_auxilaries']['categories'];

        // buzzword options
        if ( isset($buzzwords['activate']) and !empty($buzzwords['activate']) and $buzzwords['activate'] == true) {
            $roomObject->setWithBuzzwords();
        } else {
            $roomObject->setWithoutBuzzwords();
        }
        if ( isset($buzzwords['mandatory']) and !empty($buzzwords['mandatory']) and $buzzwords['mandatory'] == true ) {
            $roomObject->setBuzzwordMandatory();
        } else {
            $roomObject->unsetBuzzwordMandatory();
        }

        // tag options
        if ( isset($categories['activate']) and !empty($categories['activate']) and $categories['activate'] == true) {
            $roomObject->setWithTags();
        } else {
            $roomObject->setWithoutTags();
        }
        if ( isset($categories['mandatory']) and !empty($categories['mandatory']) and $categories['mandatory'] == true ) {
            $roomObject->setTagMandatory();
        } else {
            $roomObject->unsetBuzzwordMandatory();
        }
        if ( isset($categories['edit']) and !empty($categories['edit']) and $categories['edit'] == true ) {
            $roomObject->setTagEditedByModerator();
        } else {
            $roomObject->setTagEditedByAll();
        }

        /********* save template options ******/
        $template = $roomData['template'];

        if ( isset($template['status']) and !empty($template['status'])) {
            if ( $template['status'] == true ) {
               $roomObject->setTemplate();
            } else {
               $roomObject->setNotTemplate();
            }
         } elseif ( $roomObject->isProjectRoom()
                    or $roomObject->isCommunityRoom()
                    or $roomObject->isPrivateRoom()
                    or $roomObject->isGroupRoom()
                  ) {
            $roomObject->setNotTemplate();
         }
         if ( isset($template['template_availability'])){
            if ( $roomObject->isCommunityRoom() ){
               $roomObject->setCommunityTemplateAvailability($template['template_availability']);
            } else{
               $roomObject->setTemplateAvailability($template['template_availability']);
            }
         }
         if ( isset($template['template_description'])){
            $roomObject->setTemplateDescription($text_converter->sanitizeHTML($template['template_description']));
         }

        /***************** save room status *************/
        /*
        if ( isset($roomData['room_status']) ) {
            if ($roomData['room_status'] == '') {
                // archive
                if ($this->legacyEnvironment->isArchiveMode() ) {
                    $roomObject->backFromArchive();
                    $this->legacyEnvironment->deactivateArchiveMode();
                }
                // archive
                // old: should be impossible
                else {
                    // Fix: Find Group-Rooms if existing
                    if( $roomObject->isGrouproomActive() ) {  // GrouproomActive schmeißt fehler gucken ob er hier rein rennt wegen Kategorie einstellungen
                        $groupRoomList = $roomObject->getGroupRoomList();

                        if( !$groupRoomList->isEmpty() ) {
                            $roomObject = $groupRoomList->getFirst();

                            while($room_item) {
                                // All GroupRooms have to be opened too
                                $roomObject->open();
                                $roomObject->save();
         
                                $roomObject = $groupRoomList->getNext();
                            }
                        }
                    }
                    // ~Fix
                    $roomObject->open();
                }
            } elseif ($roomData['room_status'] == 2) {
                // template or not: template close, others archive
                if ( !$roomObject->isTemplate() ) {
                    $roomObject->moveToArchive();
                    $this->legacyEnvironment->activateArchiveMode();                             
                }
            }
        }
        // status != 2 and =! empty
        else {
            // archive
            if ($this->legacyEnvironment->isArchiveMode() ) {
                $roomObject->backFromArchive();
                $this->legacyEnvironment->deactivateArchiveMode();
            }
        }
        */
        

        /***************** save AGB *************/
        /*
        $languages = $this->legacyEnvironment->getAvailableLanguageArray();
        $current_user = $this->legacyEnvironment->getCurrentUserItem();
        $text_converter = $this->legacyEnvironment->getTextConverter();
        foreach ($languages as $language) {
            if (!empty($roomData['agb_text_'.mb_strtolower($language, 'UTF-8')])) {
               $agbtext_array[mb_strtoupper($language, 'UTF-8')] = $roomData['agb_text_'.mb_strtolower($language, 'UTF-8')];
            } else {
               $agbtext_array[mb_strtoupper($language, 'UTF-8')] = '';
            }
         }
         
        if(($agbtext_array != $roomObject->getAGBTextArray()) or ($roomData['agb_status'] != $roomObject->getAGBStatus())) {
            $roomObject->setAGBStatus($roomData['agb_status']);
            $roomObject->setAGBTextArray($agbtext_array);
            $roomObject->setAGBChangeDate();
            $current_user->setAGBAcceptance();
            $current_user->save();
         }
         
        $text_converter = $this->legacyEnvironment->getTextConverter();

        // extra todo status
        $status_array = array();
        foreach($roomData as $key => $value) {
            if(mb_substr($key, 0, 18) === 'additional_status_') {
                $status_array[mb_substr($key, 18)] = $text_converter->sanitizeHTML($value);
            }
        }

        $roomObject->setExtraToDoStatusArray($status_array);
        */
        
        return $roomObject;
    }
}