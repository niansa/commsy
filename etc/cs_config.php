<?PHP
// $Id$
//
// Release $Name$
//
// Copyright (c)2008 Iver Jackewitz
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

/** Database setup **/
$db['normal']['host']          = 'localhost';
$db['normal']['user']          = 'dev_commsy4';
$db['normal']['password']      = 'dev_commsy4';
$db['normal']['database']      = 'dev_commsy4';

/** Basic server configuration **/
// set domain for the installation of commsy
//    http://www.xyz.de/commsy/htdocs/commsy.php
//       -> [$c_commsy_domain]/commsy/htdocs/commsy.php
if ( !empty($_SERVER['HTTP_HOST']) ) {
   if ( !empty($_SERVER['SERVER_PORT'])
        and $_SERVER['SERVER_PORT'] == 443
      ) {
      $c_commsy_domain = 'https://';
   } else {
      $c_commsy_domain = 'http://';
   }
   $c_commsy_domain .= $_SERVER['HTTP_HOST'];
} else {
// $c_commsy_domain = 'http://www.xyz.de';
}
//
// Set url path to the installation of the commsy htdocs folder. This setting
// specifies the relative path to the location of the important file commsy.php.
// Example:
//    http://www.xyz.de/commsy/htdocs/commsy.php
//       -> http://www.xyz.de[$c_commsy_url_path]/commsy.php
//       -> $c_commsy_url_path = '/commsy/htdocs'
    $c_commsy_url_path = '/Source/Commsy2009/htdocs';
//
// set file path to the installation of the commsy folder
    $c_commsy_path_file = 'C:/Entwicklung/xampp/htdocs/Source/CommSy2009';

// set security key to prevent session riding
// $c_security_key = '';

// include first special commsy settings
@include_once('etc/commsy/settings.php');

// include then special config files
@include_once('etc/commsy/cookie.php');
@include_once('etc/commsy/etchat.php');
@include_once('etc/commsy/jsmath.php');
@include_once('etc/commsy/pmwiki.php');
@include_once('etc/commsy/swish-e.php');
@include_once('etc/commsy/ims.php');
@include_once('etc/commsy/soap.php');
@include_once('etc/commsy/fckeditor.php');
@include_once('etc/commsy/clamscan.php');
@include_once('etc/commsy/development.php');
@include_once('etc/commsy/autosave.php');
@include_once('etc/commsy/plugin.php');
@include_once('etc/commsy/beluga.php');

// annonymous accounts: USERID_AUTHSOURCEID
$c_annonymous_account_array = array();
?>