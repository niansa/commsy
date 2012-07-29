<?php
	require_once('classes/controller/cs_ajax_controller.php');

	class cs_ajax_widget_new_entries_controller extends cs_ajax_controller {
		/**
		 * constructor
		 */
		public function __construct(cs_environment $environment) {
			// call parent
			parent::__construct($environment);
		}
		
		public function actionGetListContent() {
			$return = array(
				"items"		=> array()
			);
			
			$itemManager = $this->_environment->getItemManager();
			$currentUser = $this->_environment->getCurrentUserItem();
			
			// collection room ids
			$room_id_array = array();
			$grouproom_list = $currentUser->getUserRelatedGroupList();
			if ( isset($grouproom_list) and $grouproom_list->isNotEmpty()) {
				$grouproom_list->reverse();
				$grouproom_item = $grouproom_list->getFirst();
				while ($grouproom_item) {
					$project_room_id = $grouproom_item->getLinkedProjectItemID();
					if ( in_array($project_room_id,$room_id_array) ) {
						$room_id_array_temp = array();
						foreach ($room_id_array as $value) {
							$room_id_array_temp[] = $value;
							if ( $value == $project_room_id) {
								$room_id_array_temp[] = $grouproom_item->getItemID();
							}
						}
						$room_id_array = $room_id_array_temp;
					}
					$grouproom_item = $grouproom_list->getNext();
				}
			}
			$project_list = $currentUser->getUserRelatedProjectList();
			if ( isset($project_list) and $project_list->isNotEmpty()) {
				$project_item = $project_list->getFirst();
				while ($project_item) {
					$room_id_array[] = $project_item->getItemID();
					$project_item = $project_list->getNext();
				}
			}
			$community_list = $currentUser->getUserRelatedCommunityList();
			if ( isset($community_list) and $community_list->isNotEmpty()) {
				$community_item = $community_list->getFirst();
				while ($community_item) {
					$room_id_array[] = $community_item->getItemID();
					$community_item = $community_list->getNext();
				}
			}
			$room_id_array_without_privateroom = $room_id_array;
			
			$itemManager->setOrderLimit(true);
			
			if (isset($room_id_array_without_privateroom) && !empty($room_id_array_without_privateroom)) {
				$new_entry_array = $itemManager->getAllNewPrivateRoomEntriesOfRoomList($room_id_array_without_privateroom);
				$new_entry_list = $itemManager->getPrivateRoomHomeItemList($new_entry_array);
			} else {
				$new_entry_list = new cs_list();
			}
			
			// prepare return
			$entry = $new_entry_list->getFirst();
			while ($entry) {
				$type = $entry->getItemType();
				if ($type == CS_LABEL_TYPE) {
					$labelManager = $this->_environment->getLabelManager();
					$entry = $labelManager->getItem($entry->getItemID());
					$type = $entry->getLabelType();
				} else {
					$manager = $this->_environment->getManager($type);
					$entry = $manager->getItem($entry->getItemID());
				}
				
				$return["items"][] = array(
					"itemId"		=> $entry->getItemID(),
					"contextId"		=> $entry->getContextID(),
					"module"		=> Type2Module($type),
					"title"			=> $entry->getTitle(),
					"image"			=> $this->getUtils()->getLogoInformationForType($type)
				);
			
				$entry = $new_entry_list->getNext();
			}
			
			$this->setSuccessfullDataReturn($return);
			echo $this->_return;
		}

		/*
		 * every derived class needs to implement an processTemplate function
		 */
		public function process() {
			// TODO: check for rights, see cs_ajax_accounts_controller
			
			// call parent
			parent::process();
		}
	}