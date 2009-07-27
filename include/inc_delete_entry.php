<?php
// $Id$
//
// Release $Name$
//
// Copyright (c)2002-2003 Matthias Finck, Dirk Fust, Oliver Hankel, Iver Jackewitz, Michael Janneck,
// Martti Jeenicke, Detlev Krause, Irina L. Marinescu, Timo Nolte, Bernd Pape,
// Edouard Simon, Monique Strauss, JosÃ© Manuel GonzÃ¡lez VÃ¡zquez
//
//    This file is part of CommSy.
//
//    CommSy is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    CommSy is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You have received a copy of the GNU General Public License
//    along with CommSy.

if (!empty($_GET['iid'])) {
   $current_item_iid = $_GET['iid'];
} else {
   include_once('functions/error_functions.php');
   trigger_error('An item id must be given.', E_USER_ERROR);
}
// Find out what to do
if ( isset($_POST['delete_option']) ) {
   $delete_command = $_POST['delete_option'];
}elseif ( isset($_GET['delete_option']) ) {
   $delete_command = $_GET['delete_option'];
} else {
   $delete_command = '';
}
if ( isset($_GET['action']) and $_GET['action'] == 'delete' ) {
   $params = $environment->getCurrentParameterArray();
   $page->addDeleteBox(curl($environment->getCurrentContextID(),$environment->getCurrentModule(),$environment->getCurrentFunction(),$params));
}
// Cancel editing
if ( isOption($delete_command, getMessage('COMMON_CANCEL_BUTTON')) ) {
   $params = $environment->getCurrentParameterArray();
   $anchor = '';
   if ( isset($_GET['section_action']) and $_GET['section_action'] == 'delete' ) {
     $anchor = 'anchor'.$params['section_iid'];
      unset($params['action']);
      unset($params['section_action']);
      unset($params['section_iid']);
      unset($params['ref_vid']);
   }elseif ( isset($_GET['annotation_action']) and $_GET['annotation_action'] == 'delete' ) {
     $anchor = 'anchor'.$params['annotation_iid'];
      unset($params['action']);
      unset($params['annotation_action']);
      unset($params['annotation_iid']);
   }elseif ( isset($_GET['discarticle_action']) and $_GET['discarticle_action'] == 'delete' ) {
      $anchor = 'anchor'.$params['discarticle_iid'];
      unset($params['action']);
      unset($params['discarticle_action']);
      unset($params['discarticle_iid']);
   }elseif ( isset($_GET['step_action']) and $_GET['step_action'] == 'delete' ) {
      $anchor = 'anchor'.$params['step_iid'];
      unset($params['action']);
      unset($params['step_action']);
      unset($params['step_iid']);
   }elseif ( isset($_GET['del_version']) ) {
      unset($params['action']);
//      $params['version_id'] = $_GET['del_version'];
      unset($params['del_version']);
   }else{
      $params['iid'] = $current_item_iid;
   }
   unset($params['action']);
   redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), $environment->getCurrentFunction(), $params,$anchor);
}
// Delete item
elseif ( isOption($delete_command, getMessage('COMMON_DELETE_BUTTON')) ) {
   if ( isset($_GET['section_action']) and $_GET['section_action'] == 'delete' ) {
      $params = $environment->getCurrentParameterArray();
      $section_manager = $environment->getSectionManager();
      $section_item = $section_manager->getItem($params['section_iid']);
      $params = array();
      $params['iid'] = $current_item_iid;
      $section_item->deleteVersion();
      $material_item = $section_item->getLinkedItem();
      $material_item->setModificationDate(getCurrentDateTimeInMySQL());
      $material_item->save();
      redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), 'detail', $params);
    }elseif ( isset($_GET['annotation_action']) and $_GET['annotation_action'] == 'delete' ) {
      $params = $environment->getCurrentParameterArray();
      $annotation_manager = $environment->getAnnotationManager();
      $annotation_item = $annotation_manager->getItem($params['annotation_iid']);
      $params = array();
      $params['iid'] = $current_item_iid;
      $annotation_item->delete();
      redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), 'detail', $params);
    }elseif ( isset($_GET['discarticle_action']) and $_GET['discarticle_action'] == 'delete' ) {
      $params = $environment->getCurrentParameterArray();
      $discarticle_manager = $environment->getDiscussionArticlesManager();
      $discarticle_item = $discarticle_manager->getItem($params['discarticle_iid']);
      unset($discarticle_manager);
      $discussion_item = $discarticle_item->getLinkedItem();
      $disc_type = $discussion_item->getDiscussionType();
      $delete_discussion = false;
      if ( $disc_type == 'threaded' ) {
         $position = $discarticle_item->getPosition();
         if ($position == 1) {
            $delete_discussion = true;
         }
      }
      $params = array();
      $params['iid'] = $current_item_iid;
      $discarticle_item->delete();
      unset($discarticle_item);
      $discussion_item->setModificationDate(getCurrentDateTimeInMySQL());
      $discussion_item->save();
      $funct = 'detail';
      if ($delete_discussion) {
         $discussion_item->delete();
         unset($discussion_item);
         $funct = 'index';
         $params = array();
      }
      redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), $funct, $params);
    }elseif ( isset($_GET['step_action']) and $_GET['step_action'] == 'delete' ) {
      $params = $environment->getCurrentParameterArray();
      $step_manager = $environment->getStepManager();
      $step_item = $step_manager->getItem($params['step_iid']);
      unset($step_manager);
      $params = array();
      $params['iid'] = $current_item_iid;
      $step_item->delete();
      $funct = 'detail';
      $todo_item = $step_item->getLinkedItem();
      $todo_item->setModificationDate(getCurrentDateTimeInMySQL());
      $todo_item->save();
      unset($step_item);
      redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), $funct, $params);
    }else{
      if ( $environment->getCurrentModule() == CS_MATERIAL_TYPE){
          if ( isset($_GET['del_version']) ) {
             $material_manager = $environment->getMaterialManager();
             $material_version_list = $material_manager->getVersionList($current_item_iid);
             $latest_version_item = $material_version_list->getFirst();
             $old_version_item = $material_version_list->getNext();
             while ($old_version_item ) {
                if ( $_GET['del_version'] == $old_version_item->getVersionID() ) {
                   $old_version_item->delete();
                   break;
                }
                $old_version_item = $material_version_list->getNext();
             }
             $params = array();
             $params['iid'] = $current_item_iid;
             redirect($environment->getCurrentContextID(), 'material', 'detail', $params);
          } else {
             $material_manager = $environment->getMaterialManager();
             $material_version_list = $material_manager->getVersionList($current_item_iid);
             $item = $material_version_list->getFirst();
             $item->delete(CS_ALL); // CS_ALL -> delete all versions of the material
          }
         redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), 'index', '');
      } elseif ( $environment->getCurrentModule() == 'configuration' ) {
         $manager = $environment->getRoomManager();
         $item = $manager->getItem($current_item_iid);
         if ( $item->isProjectRoom()
              or $item->isCommunityRoom()
              or $item->isGroupRoom()
              or $item->isPrivateRoom()
            ) {
            if ( $item->isCommunityRoom()
                 or $item->isPrivateRoom()
               ) {
               $redirect_context_id = $item->getContextID();
               $redirect_module     = 'home';
               $redirect_function   = 'index';
               $redirect_params     = array();
            } elseif ( $item->isGroupRoom() ) {
               $redirect_context_id = $item->getLinkedProjectItemID();
               $redirect_module     = CS_GROUP_TYPE;
               $redirect_function   = 'detail';
               $redirect_params     = array();
               $redirect_params['iid'] = $item->getLinkedGroupItemID();
            } elseif ( $item->isProjectRoom() ) {
               $redirect_context_id = $item->getContextID();
               $redirect_module     = 'home';
               $redirect_function   = 'index';
               $redirect_params     = array();
               // community room
               $community_list = $item->getCommunityList();
               if ( !empty($community_list) and $community_list->isNotEmpty() ) {
                  $community_item = $community_list->getFirst();
                  $redirect_context_id = $community_item->getItemID();
                  unset($community_item);
                  unset($community_list);
               }
            }
            $item->delete();
            redirect($redirect_context_id,$redirect_module,$redirect_function,$redirect_params);
         }
         redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), 'index', '');
      } elseif ( $environment->getCurrentModule() == 'account' ) {
         // do nothing, handling in page account_status
      } else {
         $manager = $environment->getManager(module2type($environment->getCurrentModule()));
         $item = $manager->getItem($current_item_id);
         $item->delete();
         redirect($environment->getCurrentContextID(), $environment->getCurrentModule(), 'index', '');
      }
   }
}

// room archive
elseif ( isOption($delete_command, getMessage('ROOM_ARCHIV_BUTTON')) ) {
   $manager = $environment->getRoomManager();
   $item = $manager->getItem($current_item_iid);
   $item->close();
   $item->save();
   if ( $environment->getCurrentModule() == CS_PROJECT_TYPE
        and $environment->inCommunityRoom()
      ) {
      $params = array();
      if (isset($item)) {
         $params['iid'] = $item->getItemID();
         redirect($environment->getCurrentContextID(),CS_PROJECT_TYPE,'detail',$params);
         unset($params);
      } else {
         redirect($environment->getCurrentContextID(),CS_PROJECT_TYPE,'index','');
      }
   } elseif ($environment->getCurrentModule() == CS_MYROOM_TYPE) {
      $params = array();
      if (isset($item)) {
         $params['iid'] = $item->getItemID();
         redirect($environment->getCurrentContextID(),CS_MYROOM_TYPE,'detail',$params);
         unset($params);
      } else {
         redirect($environment->getCurrentContextID(),CS_MYROOM_TYPE,'index','');
      }
   } elseif ($environment->getCurrentModule() == 'configuration') {
      if ( $environment->getCurrentFunction() == 'account_options' ) {
         $redirect_context_id = $environment->getCurrentContextID();
         $redirect_module     = $environment->getCurrentModule();
         $redirect_function   = $environment->getCurrentFunction();
      } else {
         $redirect_context_id = $environment->getCurrentContextID();
         $redirect_module     = 'home';
         $redirect_function   = 'index';
      }
      $redirect_params     = array();
      redirect($redirect_context_id,$redirect_module,$redirect_function,$redirect_params);
   } else {
      redirect($environment->getCurrentContextID(),$environment->getCurrentModule(),$environment->getCurrentFunction(),'');
   }
}
// user reject
elseif ( isOption($delete_command, getMessage('COMMON_USER_REJECT_BUTTON')) ) {
   // do nothing, handling in page account_status
}
?>