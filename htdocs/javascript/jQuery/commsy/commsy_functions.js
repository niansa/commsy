function resetSearchText(id){
   jQuery('#' + id).val("");
}

function handleWidth_new(id,max_width,link_name){
   var div = jQuery('#' + id);
   var inner_div = jQuery('#' + 'inner_'+id);
   var width = inner_div.scrollWidth;
   var height = inner_div.scrollHeight;
      
   if (width > max_width){
      inner_div.style.width = max_width+'px';
      if (navigator.userAgent.indexOf("MSIE") != -1){
         inner_div.style.height = (height+50)+'px';
      }
   }
}

function initTextFormatingInformation(item_id,is_shown){
   if (is_shown == false){
      jQuery('#creator_information'+item_id).hide();
   }else{
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('more','less'));
   }
   jQuery('#toggle'+item_id).click(function(){
      if(jQuery('#toggle'+item_id).attr('src').toLowerCase().indexOf('less') >= 0){
         jQuery('#creator_information'+item_id).slideUp(200);
         jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('less','more'));
      } else {
         jQuery('#creator_information'+item_id).slideDown(200);
         jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('more','less'));
      }
	});
   jQuery('#toggle'+item_id).mouseover(function(){
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('.gif','_over.gif'));
	});
   jQuery('#toggle'+item_id).mouseout(function(){
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('_over.gif','.gif'));
	});
}

function initCreatorInformations(item_id,is_shown){
   if (is_shown == false){
      jQuery('#creator_information'+item_id).hide();
   }else{
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('more','less'));
   }
   jQuery('#toggle'+item_id).click(function(){
      if(jQuery('#toggle'+item_id).attr('src').toLowerCase().indexOf('less') >= 0){
         jQuery('#creator_information'+item_id).slideUp(200);
         jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('less','more'));
      } else {
         jQuery('#creator_information'+item_id).slideDown(200);
         jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('more','less'));
      }
	});
   jQuery('#toggle'+item_id).mouseover(function(){
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('.gif','_over.gif'));
	});
   jQuery('#toggle'+item_id).mouseout(function(){
      jQuery('#toggle'+item_id).attr('src', jQuery('#toggle'+item_id).attr('src').replace('_over.gif','.gif'));
	});
}

function preInitCommSyPanels(panelTitles,panelDesc,panelDisplayed,cookieArray,sizeArray){
   jQuery(document).ready(function() {
      initCommSyPanels(panelTitles,panelDesc,panelDisplayed,cookieArray,sizeArray);
   });
}

function initCommSyPanels(panelTitles,panelDesc,panelDisplayed,cookieArray,sizeArray){
   var divs = jQuery('#commsy_panels').find('div');
   commsy_panel_index=0;
   for(var no=0;no<sizeArray.length;no++){
      if (sizeArray[no] < 31) {
         speedArray[no]=xpPanel_slideSpeed;
      } else if (sizeArray[no] < 61) {
         speedArray[no]=xpPanel_slideSpeed*2;
      } else {
         speedArray[no]=sizeArray[no];
      }
   }
   for(var no=0;no<divs.length;no++){
      if(divs[no].className == 'commsy_panel'){
         var temp_div = jQuery(divs[no]);
         var outerContentDiv = jQuery('<div></div>');
         var contentDiv = temp_div.children('div:first');
         outerContentDiv.append(contentDiv);
         outerContentDiv.attr('id', 'paneContent' + commsy_panel_index);
         outerContentDiv.attr('class', 'panelContent');

         var topBar = jQuery('<div></div>');
         topBar.attr('id', 'topBar' + commsy_panel_index);
         topBar.onselectstart = cancelXpWidgetEvent;

         var info = jQuery('<div></div>');
         info.attr('id', 'info' + commsy_panel_index);
         info.css('float', 'left');

         var span = jQuery('<span></span>');
         span.attr('id', 'span' + commsy_panel_index);
         span.html(panelTitles[commsy_panel_index].replace(/&COMMSYDHTMLTAG&/g,'</'));
         span.css('line-height', '20px');
         span.css('vertical-align', 'bottom');
         info.append(span);

         var span2 = jQuery('<span></span>');
         span2.attr('id', 'spanKlick' + commsy_panel_index);
         if(panelDesc[commsy_panel_index] == ''){
            span2.html('&nbsp;');
         } else {
            span2.html(panelDesc[commsy_panel_index]);
         }
         span2.attr('class', 'small');
         span2.css('line-height', '20px');
         span2.css('vertical-align', 'bottom');
         info.append(span2);

         topBar.css('position', 'relative');
         topBar.append(info);

         var klick = jQuery('<div></div>');
         klick.attr('id', 'klick' + commsy_panel_index);
         klick.css('height', '100%');

         var img = jQuery('<img/>');
         img.attr('id', 'showHideButton' + commsy_panel_index);
         img.attr('src', 'images/arrow_up.gif');
         img.css('float', 'right');
         klick.append(img);

         topBar.append(klick);

         if(cookieArray[commsy_panel_index]){
            cookieValue = Get_Cookie(cookieArray[commsy_panel_index]);
            if(cookieValue ==1){
               panelDisplayed[commsy_panel_index] = true;
            }else{
               panelDisplayed[commsy_panel_index] = false;
            }
         }

         if(!panelDisplayed[commsy_panel_index]){
            outerContentDiv.css('height', '0px');
            if (navigator.userAgent.indexOf("MSIE 6.0") == -1){
               contentDiv.css('top', 0 - contentDiv.offsetHeight + 'px');
               if(document.all){
                  outerContentDiv.css('display', 'none');
               }
            }
            img.attr('src', 'images/arrow_down.gif');
            klick.attr('id', klick.attr('id') + 'down');
            span.attr('id', span.attr('id') + 'down');
            span2.attr('id', span2.attr('id') + 'down');
         } else {
            klick.attr('id', klick.attr('id') + 'up');
            span.attr('id', span.attr('id') + 'up');
            span2.attr('id', span2.attr('id') + 'up');
         }

         topBar.attr('class', 'topBar');
         temp_div.append(topBar);
         temp_div.append(outerContentDiv);
         commsy_panel_index++;

         //var childrenSpan = span.getElementsByTagName('*');
         var childrenSpan = span.find('');
         var hasLink = false;
         for(var index=0; index<childrenSpan.length; index++) {
            if(childrenSpan[index].tagName == 'A'){
               hasLink = true;
            }
         }
         if(!hasLink){
            span.click(showHidePaneContentTopBar);
            span.mouseover(mouseoverTopbarBar);
            span.mouseout(mouseoutTopbarBar);
         }

         span2.click(showHidePaneContentTopBar);
         span2.mouseover(mouseoverTopbarBar);
         span2.mouseout(mouseoutTopbarBar);

         klick.click(showHidePaneContentTopBar);
         klick.mouseover(mouseoverTopbarBar);
         klick.mouseout(mouseoutTopbarBar);
      }
   }
}

function showHidePaneContentTopBar(e,inputObj){
   if(!inputObj){
      inputObj = this;
   }

   var numericId = inputObj.id.replace(/[^0-9]/g,'');
   if(inputObj.id.toLowerCase().indexOf('up')>=0){
      var klick = jQuery('#klick' + numericId + 'up');
      var span = jQuery('#span' + numericId + 'up');
      var span2 = jQuery('#spanKlick' + numericId + 'up');
      var bar = jQuery('#topBar' + numericId + 'up');
   } else {
      var klick = jQuery('#klick' + numericId + 'down');
      var span = jQuery('#span' + numericId + 'down');
      var span2 = jQuery('#spanKlick' + numericId + 'down');
      var bar = jQuery('#topBar' + numericId + 'down');
   }
   var img = jQuery('#showHideButton' + numericId);
   var obj = jQuery('#paneContent' + numericId);

   xpPanel_slideSpeed = speedArray[numericId];

   if(inputObj.id.toLowerCase().indexOf('up')>=0){
      currentlyExpandedPane = false;
      klick.attr('id', klick.attr('id').replace('up','down'));
      span.attr('id', span.attr('id').replace('up','down'));
      span2.attr('id', span2.attr('id').replace('up','down'));
      img.attr('src', img.attr('src').replace('up','down'));
      if(xpPanel_slideActive && xpPanel_slideSpeed<200){
         obj.css('display', 'block');
         xpPanel_currentDirection[obj.attr('id')] = (xpPanel_slideSpeed*-1);
         slidePane((xpPanel_slideSpeed*-1), obj.attr('id'));
      }else{
         obj.css('display', 'none');
      }
      if(cookieNames[numericId]){
         Set_Cookie(cookieNames[numericId],'0',100000);
      }
   }else{
      if(this){
         //if(currentlyExpandedPane && xpPanel_onlyOneExpandedPane){
         //   showHidePaneContent(xpPanel_slideSpeed,false,currentlyExpandedPane);
         //}
         currentlyExpandedPane = this;
      }else{
         currentlyExpandedPane = false;
      }
      klick.attr('id', klick.attr('id').replace('down','up'));
      span.attr('id', span.attr('id').replace('down','up'));
      span2.attr('id', span2.attr('id').replace('down','up'));
      img.attr('src', img.attr('src').replace('down','up'));
      if(xpPanel_slideActive && xpPanel_slideSpeed<200){
         if(document.all){
            obj.css('display', 'block');
         }
         xpPanel_currentDirection[obj.attr('id')] = xpPanel_slideSpeed;
         slidePane(xpPanel_slideSpeed,obj.attr('id'));
      }else{
         obj.css('display', 'block');
         subDiv = obj.children('div:first');
         obj.css('height', subDiv.offsetHeight + 'px');
      }
      if(cookieNames[numericId]){
         Set_Cookie(cookieNames[numericId],'1',100000);
      }
   }
   return true;
}

function mouseoverTopbarBar(){
   var numericId = this.id.replace(/[^0-9]/g,'');
   jQuery('#showHideButton' + numericId).attr('src', jQuery('#showHideButton' + numericId).attr('src').replace('.gif','_over.gif'));
   document.body.style.cursor = "pointer";
}

function mouseoutTopbarBar(){
   var numericId = this.id.replace(/[^0-9]/g,'');
   jQuery('#showHideButton' + numericId).attr('src', jQuery('#showHideButton' + numericId).attr('src').replace('_over.gif','.gif'));
   document.body.style.cursor = "default";
}

function slidePane(slideValue,id,name){
   if(slideValue!=xpPanel_currentDirection[id]){
      return false;
   }
   var activePane = jQuery('#' + id);
   if(activePane==savedActivePane){
      var subDiv = savedActiveSub;
   }else{
      var subDiv = activePane.children('div:first');
   }
   savedActivePane = activePane;
   savedActiveSub = subDiv;

   var height = activePane.height();
   var innerHeight = subDiv.height();
   height+=slideValue;
   if(height<0){
      height=0;
   }
   if(height>innerHeight){
      height = innerHeight;
   }
   if(document.all){
      activePane.css('filter', 'alpha(opacity=' + Math.round((height / subDiv.height())*100) + ')')
   }else{
      var opacity = (height / subDiv.height());
      if(opacity==0){
         opacity=0.01;
      }
      if(opacity==1){
         opacity = 0.99;
      }
      activePane.css('opacity', opacity);
   }

   if(slideValue<0){
      activePane.css('height', height + 'px');
      subDiv.css('top', height - subDiv.height() + 'px');
      if(height>0){
         setTimeout('slidePane(' + slideValue + ',"' + id + '")',10);
      }else{
         if(document.all){
            activePane.css('display', 'none');
         }
      }
   }else{
      subDiv.css('top', height - subDiv.height() + 'px');
      activePane.css('height', height + 'px');
      if(height<innerHeight){
         setTimeout('slidePane(' + slideValue + ',"' + id + '")',10);
      }
   }
}