<?PHP
// $Id$
//
// Release $Name$
//
// Copyright (c)2002-2003 Matthias Finck, Dirk Fust, Oliver Hankel, Iver Jackewitz, Michael Janneck,
// Martti Jeenicke, Detlev Krause, Irina L. Marinescu, Timo Nolte, Bernd Pape,
// Edouard Simon, Monique Strauss, Jose Mauel Gonzalez Vazquez
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

$this->includeClass(RUBRIC_FORM);

/** class for commsy form: group
 * this class implements an interface for the creation of forms in the commsy style
 */
class cs_configuration_autoaccounts_selection_form extends cs_rubric_form {

  /**
   * string - containing the headline of the form
   */
   var $_headline = NULL;
   var $_array = NULL;



  /** constructor
    * the only available constructor
    *
    * @param object environment the environment object
    *
    * @author CommSy Development Group
    */
   function cs_configuration_autoaccounts_selection_form($params) {
      $this->cs_rubric_form($params);
   }

   function setArray($array){
      $this->_array = $array;
      $temp_array = array();
      foreach($this->_array as $key =>  $data){
        $new_array= array();
        $new_array['text']= $key;
        $new_array['value']= $key;
        $temp_array[]= $new_array;
      }
      $this->_array = $temp_array;
   }
   /** init data for form, INTERNAL
    * this methods init the data for the form
    *
    * @author CommSy Development Group
    */
   function _initForm () {
      $this->setHeadline(getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_FORM_HEADLINE'));
   }

   /** create the form, INTERNAL
    * this methods creates the form with the form definitions
    *
    * @author CommSy Development Group
    */
   function _createForm () {
      $this->_form->addSelect('autoaccounts_lastname',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_LASTNAME'),getMessage('DATE_TITLE_DESC'), 1, false,false,false,'','','','',15.3);
      $this->_form->addSelect('autoaccounts_firstname',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_FIRSTNAME'),'', 1, false,false,false,'','','','',15.3);
      $this->_form->addSelect('autoaccounts_email',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_EMAIL'),'', 1, false,false,false,'','','','',15.3);
      $this->_form->addSelect('autoaccounts_account',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_ACCOUNT'),'', 1, false,false,false,'','','','',15.3);
      $this->_form->addSelect('autoaccounts_password',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_PASSWORD'),'', 1, false,false,false,'','','','',15.3);
      $this->_form->addSelect('autoaccounts_rooms',$this->_array,'',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SELECTION_ROOMS'),'', 1, false,false,false,'','','','',15.3);
      $this->_form->addEmptyline();

      $this->_form->addCheckbox('autoaccount_no_new_account_when_email_exists',1,'',$this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_NEW_ACCOUNT_WHEN_EMAIL'),$this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_NEW_ACCOUNT_WHEN_EMAIL_DESCRIPTION'),false,false,false,'','',false,false);

      $this->_form->addCheckbox('autoaccount_send_email',1,'',$this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SEND_EMAIL'),$this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_SEND_EMAIL_DESCRIPTION'),false,false,false,'','',false,false);

      $this->_form->addTextField('autoaccount_email_subject','',$this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_EMAIL_SUBJECT'),'',150,50,false,'','','','left','','',false,'','',false,false);
      
      $this->_form->addTextArea('autoaccount_email_text','',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_EMAIL_TEXT'),'','60','20','',false);

      $this->_form->addButtonBar('option',getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_CREATE_BUTTON'),getMessage('COMMON_CANCEL_BUTTON'));
   }

   /** loads the selected and given values to the form
    * this methods loads the selected and given values to the form
    *
    * @author CommSy Development Group
    */
   function _prepareValues () {
      $this->_values = array();
      if (isset($this->_form_post)) {
         $this->_values = $this->_form_post;
      }
   }


   /** specific check the values of the form
    * this methods check the entered values
    */
   function _checkValues () {
      if ( !empty($this->_form_post['autoaccount_send_email'])
           and empty($this->_form_post['autoaccount_email_subject'])
         ) {
         $this->_error_array[] = $this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_NO_SUBJECT_ERROR');
         $this->_form->setFailure('autoaccount_email_subject','');
      }
      if ( !empty($this->_form_post['autoaccount_send_email'])
           and empty($this->_form_post['autoaccount_email_text'])
         ) {
         $this->_error_array[] = $this->_translator->getMessage('COMMON_CONFIGURATION_AUTOACCOUNTS_NO_TEXT_ERROR');
         $this->_form->setFailure('autoaccount_email_text','');
      }
   }
}
?>