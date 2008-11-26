<?php
// $Id:

header("Content-type: text/css");
// load required classes
chdir('../..');
include_once('etc/cs_constants.php');
include_once('etc/cs_config.php');
include_once('classes/cs_environment.php');

// create environment of this page
$color = $cs_color['DEFAULT'];

// find out the room we're in
if (!empty($_GET['cid'])) {
   $cid = $_GET['cid'];
   $environment = new cs_environment();
   $environment->setCurrentContextID($cid);
   $room = $environment->getCurrentContextItem();
   $portal = $environment->getCurrentPortalItem();
   $color = $room->getColorArray();
}
?>


/*General Settings */
body {
	margin: 0px;
	padding: 0px;
   font-family: 'Trebuchet MS','lucida grande',tahoma,"ms sans serif",verdana,arial,sans-serif;
   font-size:80%;
   font-size-adjust:none;
   font-stretch:normal;
   font-style:normal;
   font-variant:normal;
   font-weight:normal;
}

.fade-out-link{
    font-size:8pt;
    color:black;
}

img {
	border: 0px;
}


/*Hyperlinks*/
a {
	color: <?php echo($color['hyperlink'])?>;
	text-decoration: none;
}

a:hover, a:active {
	text-decoration: underline;
}


/* Font-Styles */
.infocolor{
	color: <?php echo($color['info_color'])?>;
}

.disabled, .key .infocolor{
	color: <?php echo($color['disabled'])?>;
}

.changed {
	color: <?php echo($color['warning'])?>;
	font-size: 8pt;
}

.infoborder{
    border-top: 1px solid <?php echo($color['disabled'])?>;
    padding-top:10px;
}

.listinfoborder{
    border-top: 1px solid <?php echo($color['disabled'])?>;
    margin:5px 0px;
}

.infoborder_display_content{
    width: 71%;
}

.required {
	color: <?php echo($color['warning'])?>;
	font-weight: bold;
}

.normal{
	font-size: 10pt;
}

.handle_width{
    overflow:auto;
    padding-bottom:3px;
}

.handle_width_border{
    overflow:auto;
    padding:3px;
    border: 1px solid <?php echo($color['info_color'])?>;
}

.desc {
	font-size: 8pt;
}

.bold{
	font-size: 10pt;
	font-weight: bold;
}


/* Room Design */
#main{
	padding: 10px 10px 0px 10px;
}

div.page_header_border{
   padding:0px 20px;
   height: 8px;
   background-color:white;
}


#page_header{
   clear:both;
   padding:0px 10px 0px 10px;
   height: 70px;
   background-color: white;
   font-size:8pt;
}

#page_header_logo{
   font-size:10pt;
   height: 70px;
   vertical-align:bottom;
}

#page_header_logo table{
	height:70px;
}

#page_header_logo td{
	vertical-align:bottom;
}

#page_header_logo h1{
	font-size:24pt;
	font-weight:bold;
}

div.page_header_personal_area{
   float:right;
   width: 40%;
   padding:5px 0px 0px 0px;
}


div.content_fader{
    margin:0px;
    padding: 0px 3px;
    background: url(images/bg-<?php echo($color['schema'])?>.jpg) repeat-x;
}


div.content{
    padding:0px;
    margin:0px;
    heigth:100%;
    background-color: <?php echo($color['content_background'])?>;
}

div.content_display_width{
    width:70%;
}

div.index_content_display_width{
    width:71%;
}

div.frame_bottom {
	position:relative;
	font-size: 1px;
	border-left: 2px solid #C3C3C3;
	border-right: 2px solid #C3C3C3;
	border-bottom: 2px solid #C3C3C3;
}

div.content_bottom {
	position:relative; width: 100%;
}

/*Panel Style*/
#commsy_panels .commsy_panel, #commsy_panel_form .commsy_panel{
   margin:0px;
}

#commsy_panels .panelContent, #commsy_panel_form .panelContent{
   padding:0px;
   overflow:hidden;
   position:relative;
}

#commsy_panels .small, #commsy_panel_form .small{
   font-size:8pt;
}

#commsy_panels .panelContent div, #commsy_panel_form .panelContent div{
   position:relative;
}

#commsy_panels .commsy_panel .topBar, #commsy_panel_form .commsy_panel .topBar{
   background:url(images/tab_fader_<?php echo($color['schema'])?>.gif) repeat-x;
   background-color:<?php echo($color['tabs_background'])?>;
   color:<?php echo($color['tabs_title'])?>;
   padding: 0px 0px;
   height:20px;
   overflow:hidden;
}

#commsy_panels .commsy_panel .topBar span, #commsy_panel_form .commsy_panel .topBar span{
   line-height:20px;
   vertical-align:baseline;
   color:<?php echo($color['tabs_title'])?>;
   font-weight:bold;
   float:left;
   padding-left:5px;
}

#commsy_panels .commsy_panel .topBar img, #commsy_panel_form .commsy_panel .topBar img{
   float:right;
   cursor:pointer;
}

#otherContent{  /* Normal text content */
   float:left;  /* Firefox - to avoid blank white space above panel */
   padding-left:10px;   /* A little space at the left */
}

ul.item_list {
   margin: 3px 0px 2px 2px;
   padding: 0px 0px 3px 15px;
   list-style: circle;
}



/* Tab Style */
#tabs_frame {
   position:relative;
   background:url(images/tab_menu_fader_<?php echo($color['schema'])?>.gif) repeat-x;
   background-color: <?php echo($color['tabs_background'])?>;
   padding:0px;
   margin:0px;
   font-weight: bold;
}

#tablist{
	margin:0px;
	padding:0px 10px;
	white-space:nowrap;
}

#tabs {
   position:relative;
   width: 100%;
   border-bottom: 1px solid <?php echo($color['tabs_title'])?>;
   padding:4px 0px 3px 0px;
   margin:0px;
   font-weight: bold;
   font-size: 10pt;
}

div.tabs_bottom {
   position:relative;
   width: 100%;
   border-top: 1px solid <?php echo($color['tabs_title'])?>;
   padding:4px 0px 3px 0px;
   margin:0px;
   font-weight: bold;
   font-size: 10pt;
}

span.navlist{
   color:<?php echo($color['headline_text'])?>;
}
a.navlist{
   color:<?php echo($color['headline_text'])?>;
   padding:4px 6px 3px 6px;
   border-right:1px solid <?php echo($color['headline_text'])?>;
   text-decoration:none;
   font-size: 10pt;
}

a.navlist_current{
   color:<?php echo($color['headline_text'])?>;
   padding:4px 6px 3px 6px;
   border-right:1px solid <?php echo($color['headline_text'])?>;
   text-decoration:none;
   background-image:url(images/tab_menu_fader_aktiv_<?php echo($color['schema'])?>.gif) repeat-x;
   background-color:<?php echo($color['tabs_focus'])?>;
}

a.navlist_current:hover, a.navlist_current:active, a.navlist:hover{
   color:<?php echo($color['headline_text'])?>;
   padding:4px 6px 3px 6px;
   text-decoration:none;
   background-image:url(images/tab_menu_fader_aktiv_<?php echo($color['schema'])?>.gif) repeat-x;
   background-color:<?php echo($color['tabs_focus'])?>;
}

a.navlist:active{
   color:<?php echo($color['headline_text'])?>;
   padding:4px 6px 3px 6px;
   text-decoration:none;
}

a.navlist_help, a.navlist_help:hover, a.navlist_help:active{
   color:<?php echo($color['headline_text'])?>;
   padding:4px 6px 3px 3px;
   text-decoration:none;
}

/*Headlines*/
h1{
	margin:0px;
	padding-left:0px 0px 0px 10px;
	font-size:30px;
}

.pagetitle{
	margin:0px;
   padding-top:5px;
   font-size: 16pt;
   font-weight:bold;
}


/*Special Designs*/
.top_of_page {
	padding:5px 20px 3px 20px;
	font-size: 8pt;
	color: <?php echo($color['info_color'])?>;
}

.top_of_page a{
	color: <?php echo($color['info_color'])?>;
}

#form_formatting_box{
   margin-top:5px;
   margin-bottom:0px;
   width:93%;
   padding:5px;
   border: 1px #B0B0B0 dashed;
   background-color:<?php echo($color['boxes_background'])?>;
}
.form_formatting_checkbox_box{
   margin-top:0px;
   margin-bottom:0px;
   width:93%;
   padding:5px 10px 5px 10px;
   border: 1px #B0B0B0 dashed;
   background-color:<?php echo($color['boxes_background'])?>;
}

#template_information_box{
   margin-top:5px;
   margin-bottom:0px;
   padding:5px;
   border: 1px #B0B0B0 dashed;
   background-color:<?php echo($color['boxes_background'])?>;
}

#profile_title{
   background:url(images/detail_fader_<?php echo($color['schema'])?>.gif) center repeat-x;
   background-color:<?php echo($color['tabs_background'])?>;
   color:<?php echo($color['headline_text'])?>;
   vertical-align:top;
   margin:0px;
   padding:10px;
}

#profile_content{
   margin-bottom:20px;
   padding:0px
   background-color: #FFFFFF;
   border: 1px solid <?php echo($color['tabs_background'])?>;
}


