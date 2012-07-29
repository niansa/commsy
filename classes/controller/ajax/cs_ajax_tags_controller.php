<?php
	require_once('classes/controller/cs_ajax_controller.php');
	
	class cs_ajax_tags_controller extends cs_ajax_controller {
		/**
		 * constructor
		 */
		public function __construct(cs_environment $environment) {
			// call parent
			parent::__construct($environment);
		}
		
		public function actionCreateNewTag() {
			if($this->accessGranted()) {
				$current_user = $this->_environment->getCurrentUserItem();
				$tag_manager = $this->_environment->getTagManager();
				
				$tagName = trim($this->_data["tagName"]);
				$parentId = $this->_data["parentId"];
				$roomId = $this->_data["roomId"];
				
				if ($parentId == null) {
					if ($roomId == null) {
						$rootTagItem = $tag_manager->getRootTagItemFor($this->_environment->getCurrentContextID());
					} else {
						$rootTagItem = $tag_manager->getRootTagItemFor($roomId);
					}
					
					$parentId = $rootTagItem->getItemID();
				}
				
				$parentTagItem = $tag_manager->getItem($parentId);
				
				// check if empty
				if($tagName === "") {
					$this->setErrorReturn("110", "tag is empty", array());
					echo $this->_return;
				} else {
					$tag_item = $tag_manager->getNewItem();
					$tag_item->setTitle($tagName);
					$tag_item->setContextID($this->_environment->getCurrentContextID());
					$tag_item->setCreatorItem($current_user);
					$tag_item->setCreationDate(getCurrentDateTimeInMySQL());
					$tag_item->setPosition($parentId, $parentTagItem->getChildrenList()->getCount() + 1);
					$tag_item->save();
					
					$this->setSuccessfullDataReturn(array("tagId" => $tag_item->getItemID()));
					echo $this->_return;
				}
			}
		}
		
		public function actionRenameTag() {
			if($this->accessGranted()) {
				$current_user = $this->_environment->getCurrentUserItem();
		
				$newTagName = trim($this->_data["newTagName"]);
				$tagId = $this->_data["tagId"];
		
				// check if empty
				if($newTagName === "") {
					$this->setErrorReturn("110", "tag is empty", array());
					echo $this->_return;
				} else {
					$tag_manager = $this->_environment->getTagManager();
					
					$tag_item = $tag_manager->getItem($tagId);
					$tag_item->setTitle($newTagName);
					$tag_item->setModificatorItem($current_user);
					$tag_item->setModificationDate(getCurrentDateTimeInMySQL());
					$tag_item->save();
						
					$this->setSuccessfullDataReturn(array());
					echo $this->_return;
				}
			}
		}
		
		public function actionUpdateTreeStructure() {
			if($this->accessGranted()) {
				$parentId = $this->_data["parentId"];
				$children = $this->_data["children"];
				
				$tag_manager = $this->_environment->getTagManager();
				$tag2tag_manager = $this->_environment->getTag2TagManager();
				
				// process all children
				foreach($children as $childIndex => $childId) {
					// get item
					$childItem = $tag_manager->getItem($childId);
					
					// delete all references from/to this child id
					// ...?
					
					// set new position
					$childItem->setPosition($parentId, $childIndex + 1);
					
					// save
					$childItem->save();
				}
				
				$this->setSuccessfullDataReturn(array());
				echo $this->_return;
			}
		}
		
		public function actionUpdateTreeRoots() {
			if ($this->accessGranted()) {
				$rootIds = $this->_data["rootIds"];
				$roomId = $this->_data["roomId"];
				
				// get the root tag item
				$tagManager = $this->_environment->getTagManager();
				$currentContext = $this->_environment->getCurrentContextItem();
				$tag2TagManager = $this->_environment->getTag2TagManager();
				
				if ($roomId == null) {
					$rootTagItem = $tagManager->getRootTagItemFor($currentContext->getItemID());
				} else {
					$rootTagItem = $tagManager->getRootTagItemFor($roomId);
				}
				
				
				// set all root ids as direct childs of the root tag - like in actionUpdateTreeStructure
				foreach($rootIds as $rootIndex => $rootId) {
					// get item
					$rootItem = $tagManager->getItem($rootId);
					
					// set new position
					$rootItem->setPosition($rootTagItem->getItemID(), $rootIndex + 1);
					
					// save
					$rootItem->save();
				}
				
				$this->setSuccessfullDataReturn(array());
				echo $this->_return;
			}
		}
		
		public function actionUpdateLinkedItem() {
			$return = array();
		
			// get request data
			$item_id = $this->_data['item_id'];
			$link_id = $this->_data['link_id'];
			$checked = $this->_data['checked'];
		
			// get item
			$item_manager = $this->_environment->getItemManager();
			$temp_item = $item_manager->getItem($item_id);
			if(isset($temp_item)) {
				if($temp_item->getItemType() == 'label'){
					$label_manager = $this->_environment->getLabelManager();
					$label_item = $label_manager->getItem($temp_item->getItemID());
					$manager = $this->_environment->getManager($label_item->getLabelType());
				}else{
					$manager = $this->_environment->getManager($temp_item->getItemType());
				}
				$item = $manager->getItem($item_id);
			}
		
			// TODO: implement - users are not allowed to remove themself from the "All Members" group
		
			// get ids of linked items
			$selected_ids = $this->getLinkedItemIDArray($item);
		
			// update id array
			if($checked === true) {
				// add
				$selected_ids[] = $link_id;
				$selected_ids = array_unique($selected_ids);	// ensure uniqueness
		
				// get linked item
				$temp_item = $item_manager->getItem($link_id);
		
				if(isset($temp_item)) {
					$manager = $this->_environment->getManager($temp_item->getItemType());
					$linked_item = $manager->getItem($link_id);
				}
		
				// collect new item information
				$entry = array();
				$user = $this->_environment->getCurrentUser();
				$converter = $this->_environment->getTextConverter();
				$translator = $this->_environment->getTranslationObject();
		
				$type = $linked_item->getType();
				if($type === 'label') {
					$type = $linked_item->getLabelType();
				}
		
				$logoInformation = $this->getUtils()->getLogoInformationForType($type);
				$text = $logoInformation["text"];
				$img = $logoInformation["img"];
		
				$link_creator_text = $text . ' - ' . $translator->getMessage('COMMON_LINK_CREATOR') . ' ' . $entry['creator'];
		
				switch($type) {
					case CS_DISCARTICLE_TYPE:
						$linked_iid = $linked_item->getDiscussionID();
						$discussion_manager = $this->_environment->getDiscussionManager();
						$linked_item = $discussion_manager->getItem($linked_iid);
						break;
					case CS_SECTION_TYPE:
						$linked_iid = $linked_item->getLinkedItemID();
						$material_manager = $this->_environment->getMaterialManager();
						$linked_item = $material_manager->getItem($linked_iid);
						break;
					default:
						$linked_iid = $linked_item->getItemID();
				}
		
				$entry['linked_iid'] = $linked_iid;
		
				$module = Type2Module($type);
		
				if($linked_item->isNotActivated() && !($linked_item->getCreatorID() === $user->getItemID() || $user->isModerator())) {
					$activating_date = $linked_item->getActivatingDate();
					if(strstr($activating_date, '9999-00-00')) {
						$link_creator_text .= ' (' . $translator->getMessage('COMMON_NOT_ACTIVATED') . ')';
					} else {
						$link_creator_text .= ' (' . $translator->getMessage('COMMON_ACTIVATING_DATE') . ' ' . getDateInLang($linked_item->getActivatingDate()) . ')';
					}
		
					if($module === CS_USER_TYPE) {
						$title = $linked_item->getFullName();
					} else {
						$title = $linked_item->getTitle();
					}
					$title = $converter->text_as_html_short($title);
		
					$entry['module'] = $module;
					$entry['img'] = $img;
					$entry['title'] = $link_creator_text;
					$entry['link_text'] = $title;
				} else {
					if($module === CS_USER_TYPE) {
						$title = $linked_item->getFullName();
					} else {
						$title = $linked_item->getTitle();
					}
					$title = $converter->text_as_html_short($title);
		
					$entry['module'] = $module;
					$entry['img'] = $img;
					$entry['title'] = $link_creator_text;
					$entry['link_text'] = $title;
				}
		
				$return['linked_item'] = $entry;
			} else {
				// remove
				if(($offset = array_search($link_id, $selected_ids)) !== false) array_splice($selected_ids, $offset, 1);
			}
		
			// update item
			if(isset($item)) {
				if($item->isA(CS_LABEL_TYPE) && $item->getLabelType() == CS_BUZZWORD_TYPE) {
					$item->saveLinksByIDArray($selected_ids);
				} else {
					$item->setLinkedItemsByIDArray($selected_ids);
					$item->save();
				}
			}
		
			$this->setSuccessfullDataReturn($return);
			echo $this->_return;
		}
		
		public function actionMergeTags() {
			if($this->accessGranted()) {
				$tagIdOne = $this->_data["idOne"];
				$tagIdTwo = $this->_data["idTwo"];
		
				// check if both are different from each other
				if($tagIdOne == $tagIdTwo) {
					$this->setErrorReturn("108", "can't merge two tags with same id", array());
					echo $this->_return;
				} else {
					// merge them
					$tag2TagManager = $this->_environment->getTag2TagManager();
					$tag2TagManager->combine($tagIdOne, $tagIdTwo, $tagIdOne);
						
					// get both
					$tagManager = $this->_environment->getTagManager();
					$tagItemOne = $tagManager->getItem($tagIdOne);
					$tagItemTwo = $tagManager->getItem($tagIdTwo);
					
					$tagOne = $tagItemOne->getTitle();
					$tagTwo = $tagItemTwo->getTitle();
					$newName = $tagOne . '/' . $tagTwo;
						
					$this->setSuccessfullDataReturn(array(
							"tagOne"	=> $tagOne,
							"tagTwo"	=> $tagTwo,
							"newTag"	=> $newName)
					);
					echo $this->_return;
				}
			}
		}
		
		public function actionSortABC() {
			if ($this->accessGranted()) {
				$tag2TagManager = $this->_environment->getTag2TagManager();
				$tagManager = $this->_environment->getTagManager();
				
				$roomId = $this->_data["roomId"];
				
				if ($roomId == null) {
					$rootTagItem = $tagManager->getRootTagItemFor($this->_environment->getCurrentContextID());
				} else {
					$rootTagItem = $tagManager->getRootTagItemFor($roomId);
				}
				
				$tag2TagManager->sortRecursiveABC($rootTagItem->getItemID());
				
				$this->setSuccessfullDataReturn(array());
				echo $this->_return;
			}
		}
		
		public function actionDeleteTag() {
			if ($this->accessGranted()) {
				$tagId = $this->_data["tagId"];
				
				$tagManager = $this->_environment->getTagManager();
				
				$tagManager->delete($tagId);
				
				$this->setSuccessfullDataReturn(array());
				echo $this->_return;
			}
		}
		
		public function actionPerformRequest() {
			$return = array();
		
			$current_context = $this->_environment->getCurrentContextItem();
			$current_user = $this->_environment->getCurrentUser();
			$session = $this->_environment->getSessionItem();
		
			// get request data
			$item_id = $this->_data['item_id'];
			$roomId = $this->_data["roomId"];
			$module = $this->_data['module'];
			$current_page = $this->_data['current_page'];
			$restrictions = $this->_data['restrictions'];
		
			// get item
			$item_manager = $this->_environment->getItemManager();
			$temp_item = $item_manager->getItem($item_id);
			if(isset($temp_item)) {
				if($temp_item->getItemType() == 'label'){
					$label_manager = $this->_environment->getLabelManager();
					$label_item = $label_manager->getItem($temp_item->getItemID());
					$manager = $this->_environment->getManager($label_item->getLabelType());
				}else{
					$manager = $this->_environment->getManager($temp_item->getItemType());
				}
				$item = $manager->getItem($item_id);
			}
			// get ids of linked items
			$selected_ids = ($item_id !== "NEW") ? $this->getLinkedItemIDArray($item) : array();
		
			// build item list
			$item_list = new cs_list();
			$item_ids = array();
			$count_all = 0;
				
			if(!($item_id === "NEW" && $restrictions['only_linked'] === true)) {
				// get current room modules
				$room_modules = array();
				$current_room_modules = $current_context->getHomeConf();
				if(!empty($current_room_modules)) $room_modules = explode(',', $current_room_modules);
		
				$rubric_array = array();
				foreach($room_modules as $room_module) {
					list($name, $display) = explode('_', $room_module);
		
					if($display != 'none'	&&	!($this->_environment->inPrivateRoom() && $name == 'user') &&
							!(	$name == CS_USER_TYPE && (
									$module == CS_MATERIAL_TYPE ||
									$module == CS_DISCUSSION_TYPE ||
									$module == CS_ANNOUNCEMENT_TYPE ||
									$module == CS_TOPIC_TYPE))) {
						$rubric_array[] = $name;
					}
				}
		
				if($module == CS_USER_TYPE) {
					$rubric_array = array();
		
					if($current_context->withRubric(CS_GROUP_TYPE)) $rubric_array[] = CS_GROUP_TYPE;
					if($current_context->withRubric(CS_INSTITUTION_TYPE)) $rubric_array[] = CS_INSTITUTION_TYPE;
		
					// $interval = 100;
				}
		
				// perform rubric restriction
				if(!empty($restrictions['rubric']) && $restrictions['rubric'] !== "all") {
					$rubric_array = array();
					$rubric_array[] = $restrictions['rubric'];
				}
		
				if($restrictions['only_linked'] === true && empty($selected_ids)) $rubric_array = array();
		
				foreach($rubric_array as $rubric) {
					$rubric_list = new cs_list();
					$rubric_manager = $this->_environment->getManager($rubric);
		
					if(isset($rubric_manager) && $rubric != CS_MYROOM_TYPE) {
						if($rubric != CS_PROJECT_TYPE) {
							if ($roomId !== null) {
								$rubric_manager->setContextLimit($roomId);
							} else {
								$rubric_manager->setContextLimit($this->_environment->getCurrentContextID());
							}
						}
		
						if($rubric == CS_DATE_TYPE) $rubric_manager->setWithoutDateModeLimit();
		
						if($rubric == CS_USER_TYPE) {
							$rubric_manager->setUserLimit();
		
							if($current_user->isUser()) $rubric_manager->setVisibleToAllAndCommsy();
							else $rubric_manager->setVisibleToAll();
						}
		
						$count_all += $rubric_manager->getCountAll();
		
						// set restrictions
						if(!empty($restrictions['search'])) $rubric_manager->setSearchLimit($restrictions['search']);
						if($restrictions['only_linked'] === true) $rubric_manager->setIDArrayLimit($selected_ids);
						if($restrictions['type'] == 2) $rubric_manager->showNoNotActivatedEntries();
		
						$rubric_manager->selectDistinct();
						$rubric_list = $rubric_manager->get();
		
						// show hidden entries only if user is moderator or owner
						if($restrictions['type'] != 2 && !$current_user->isModerator()) {
							// check if user is owner
							$entry = $rubric_list->getFirst();
							while($entry) {
								if($entry->isNotActivated() && $entry->getCreatorID() != $current_user->getItemID()) {
									// remove item from list
									$rubric_list->removeElement($entry);
								}
		
								$entry = $rubric_list->getNext();
							}
						}
		
						// add rubric list to item list
						$item_list->addList($rubric_list);
		
						$temp_rubric_ids = $rubric_manager->getIDArray();
						if(!empty($temp_rubric_ids)) {
							//$session->setValue('cid'.$environment->getCurrentContextID().'_item_attach_index_ids', $rubric_ids);
							$item_ids = array_merge($item_ids, $temp_rubric_ids);
						}
					}
				}
			}
		
			$interval = CS_LIST_INTERVAL;
			$from = $current_page * $interval;
		
			// get sublist - paging
			$sublist = $item_list->getSublist($from, $interval);
		
			// prepare return
			$return['list'] = array();
			$item = $sublist->getFirst();
			while($item) {
				$entry = array();
		
				$entry['item_id']			= $item->getItemID();
				$entry['title']				= $item->getTitle();
				$entry['modification_date']	= $item->getModificationDate();
				$entry['modificator']		= $item->getModificatorItem()->getFullName();
				$entry['system_label']		= $item->isSystemLabel();
		
				$entry['checked'] = false;
				if(in_array($item->getItemID(), $selected_ids)) $entry['checked'] = true;
		
				$return['list'][] = $entry;
				$item = $sublist->getNext();
			}
			$return['paging']['pages'] = ceil(/*$count_all*/count($item_ids) / $interval);
			$return['num_selected_total'] = count($selected_ids);
				
			$this->setSuccessfullDataReturn($return);
			echo $this->_return;
		}
		
		public function actionGetInitialData() {
			$return = array();
		
			// get request data
			$module = $this->_data['module'];
		
			$current_context = $this->_environment->getCurrentContextItem();
			$translator = $this->_environment->getTranslationObject();
		
			// get available rubrics
			$rubrics = array();
		
			// add all
			$rubrics[] = array(
					'value'		=> 'all',
					'text'		=> $translator->getMessage('ALL'),
					'disabled'	=> false
			);
		
			// add disabled
			$rubrics[] = array(
					'value'		=> '-1',
					'text'		=> '-------------------------',
					'disabled'	=> true
			);
		
			// add rubrics
			$current_room_modules = $current_context->getHomeConf();
			$room_modules = array();
			if(!empty($current_room_modules)) $room_modules = explode(',', $current_room_modules);
		
			foreach($room_modules as $module) {
				list($name, $display) = explode('_', $module);
		
				if($display != 'none'	&& (	$name != CS_USER_TYPE || (	$module != CS_MATERIAL_TYPE &&
						$module != CS_DISCUSSION_TYPE &&
						$module != CS_ANNOUNCEMENT_TYPE &&
						$module != CS_TOPIC_TYPE))
						&& $name != CS_PROJECT_TYPE
						&& !$this->_environment->isPlugin($name)
						&& !($this->_environment->inPrivateRoom() && $name == CS_MYROOM_TYPE)) {
					// determ rubric text
					switch(mb_strtoupper($name, 'UTF-8')) {
						case 'ANNOUNCEMENT':
							$text = $translator->getMessage('ANNOUNCEMENT_INDEX');
							break;
						case 'DATE':
							$text = $translator->getMessage('DATE_INDEX');
							break;
						case 'DISCUSSION':
							$text = $translator->getMessage('DISCUSSION_INDEX');
							break;
						case 'GROUP':
							$text = $translator->getMessage('GROUP_INDEX');
							break;
						case 'INSTITUTION':
							$text = $translator->getMessage('INSTITUTION_INDEX');
							break;
						case 'MATERIAL':
							$text = $translator->getMessage('MATERIAL_INDEX');
							break;
						case 'PROJECT':
							$text = $translator->getMessage('PROJECT_INDEX');
							break;
						case 'TODO':
							$text = $translator->getMessage('TODO_INDEX');
							break;
						case 'TOPIC':
							$text = $translator->getMessage('TOPIC_INDEX');
							break;
						case 'USER':
							$text = $translator->getMessage('USER_INDEX');
							break;
						default:
							$text = $translator->getMessage('COMMON_MESSAGETAG_ERROR'.' ('.$name.') '.__FILE__.'('.__LINE__.') ' );
							break;
					}
		
					// add rubric
					$rubrics[] = array(
							'value'		=> $name,
							'text'		=> $text,
							'disabled'	=> false
					);
				}
			}
		
			// append to return
			$return['rubrics'] = $rubrics;
				
			$this->setSuccessfullDataReturn($return);
			echo $this->_return;
		}
		
		private function getLinkedItemIDArray($item) {
			$selected_ids = array();
			if(isset($item)) {
				$type = $item->getItemType();
				if($type == CS_USER_TYPE) {
					if($this->_environment->inCommunityRoom()) $selected_ids = $item->getLinkedItemIDArray(CS_INSTITUTION_TYPE);
					else $selected_ids = $item->getLinkedItemIDArray(CS_GROUP_TYPE);
				} elseif(isset($item) && $item->isA(CS_BUZZWORD_TYPE)) {
					$selected_ids = $item->getAllLinkedItemIDArrayLabelVersion();
				} else {
					$selected_ids = $item->getAllLinkedItemIDArray();
				}
			}
			return $selected_ids;
		}

		/*
		 * every derived class needs to implement an processTemplate function
		 */
		public function process() {
			// call parent
			parent::process();
		}
		
		private function accessGranted() {
			$current_user = $this->_environment->getCurrentUserItem();
			$current_context = $this->_environment->getCurrentContextItem();
			
			if (!$current_user->isUser() || ( !$current_context->isTagEditedByAll() && !$current_user->isModerator)) {
				return false;
				/*
				 * $params = array();
				$params['environment'] = $environment;
				$params['with_modifying_actions'] = true;
				$errorbox = $class_factory->getClass(ERRORBOX_VIEW,$params);
				unset($params);
				$errorbox->setText($translator->getMessage('LOGIN_NOT_ALLOWED'));
				$page->add($errorbox);
				 */
			} else {
				return true;
			}
		}
	}
?>

<?php
/*
				 * // Find out what to do
   $iid = 0;
   $delete_iid = 0;
   $command = '';
   $delete_option = '';
   if(isset($_POST)) {
      foreach($_POST as $key => $value) {
         if(empty($command) && mb_substr($key, 0, 6) == 'option') {
            $command = $value;
            $iid = mb_substr($key, 7);
         }
         
         if(empty($delete_option) && mb_substr($key, 0, 13) == 'delete_option') {
            $delete_option = $value;
            $delete_iid = mb_substr($key, 14);
         }
         
         if(!empty($command) && !empty($delete_option)) {
            break;
         }
      }
   }
   
   // delete box
   $deleteOverlay = false;
   if(isOption($command, $translator->getMessage('COMMON_DELETE_BUTTON'))) {
      $params = $environment->getCurrentParameterArray();
      $params['delete_id'] = $iid;
	  $page->addDeleteBox(curl($environment->getCurrentContextID(),$environment->getCurrentModule(),$environment->getCurrentFunction(),$params));
	  $deleteOverlay = true;
   }
   // change option
   else if(isOption($command, $translator->getMessage('BUZZWORDS_CHANGE_BUTTON'))) {
      $change_id = $iid;
   }
   
   ##########################################
   ## handle messages from delete box
   #
   // delete option
   if(isOption($delete_option, $translator->getMessage('COMMON_DELETE_BUTTON'))) {
      if(isset($_GET['delete_id'])) {
         $delete_id = $_GET['delete_id'];
      } else {
         $delete_id = $delete_iid;
      }
      
   }
   // cancel option
   else if(isOption($delete_option, $translator->getMessage('COMMON_CANCEL_BUTTON'))) {
      $params = $environment->getCurrentParameterArray();
      unset($params['delete_id']);
      redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), $environment->getCurrentFunction(), $params);
   }
   #
   ##
   ##########################################

   // attach items
   if ( !empty($_POST) && !$deleteOverlay ) {
      $link_items = false;
      foreach ( $_POST as $key => $value ) {
         if ( $value == $translator->getMessage('COMMON_ITEM_NEW_ATTACH')
              and strstr($key,'right_box_option')
            ) {
            $tag_id = substr($key,strpos($key,'#')+1);
            $_GET['iid'] = $tag_id;
            if ( !empty($_POST['module'])
                 and $_POST['module'] != 'home'
               ) {
               $_GET['selrubric'] = $_POST['module'];
            }
            $_POST['right_box_option'] = $translator->getMessage('COMMON_ITEM_NEW_ATTACH');
            $link_items = true;
            $session->unsetValue('cid'.$environment->getCurrentContextID().'_linked_items_index_selected_ids2');
            break;
         }
      }
      if ( !$link_items
           and !empty($_POST['option'])
           and isOption($_POST['option'], $translator->getMessage('COMMON_ITEM_ATTACH'))
         ) {
         $link_items = true;
      }
      if ( !$link_items
           and !empty($_POST)
           and empty($_POST['option'])
           and empty($change_id)
           and empty($delete_id)
         ) {
         $_GET['attach_view'] = 'yes';
         $_GET['attach_type'] = 'item';
         $link_items = true;
      }
      if ( $link_items ) {
         include_once('pages/item_attach.php');
      }
   }
   
   // Show form and/or save item
   // Initialize the form
   $class_params= array();
   $class_params['environment'] = $environment;
   $form = $class_factory->getClass(BUZZWORDS_FORM,$class_params);
   unset($class_params);
   
   // Load form data from postvars
   if ( !empty($_POST) ) {
      $form->setFormPost($_POST);
   }
   
   $form->prepareForm();
   $form->loadValues();
   
   // Save item
   if ( !empty($delete_id) or !empty($change_id) ) {
      $buzzword_manager  = $environment->getLabelManager();
      // delete
      if(isset($delete_id) && !empty($delete_id)) {
         $buzzword_item = $buzzword_manager->getItem($delete_id);
         if(!empty($buzzword_item)) {
            $buzzword_item->delete();
         }
         unset($delete_id);
         unset($tag_item);
      }
      // change
      else if(isset($change_id) && !empty($change_id)) {
         $buzzword_item = $buzzword_manager->getItem($change_id);
         if(!empty($buzzword_item)) {
            $buzzword_item->setName($_POST['buzzword#' . $change_id]);
            $buzzword_item->save();
         }
         unset($change_id);
         unset($tag_item);
      }
      unset($tag_manager);
      
      $params = array();
      if (empty($delete_id)) {
         $params['focus_element_onload'] = $change_id;
      }
      redirect($environment->getCurrentContextID(),'buzzwords', 'edit', $params);
   }elseif (!empty($command) and isOption($command, $translator->getMessage('BUZZWORDS_NEW_BUTTON'))){
      
   }elseif (!empty($command) and isOption($command, $translator->getMessage('BUZZWORDS_COMBINE_BUTTON'))){
      
   }

  
				 */
				 ?>