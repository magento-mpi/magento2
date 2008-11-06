/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function setLocation(url){
    window.location.href = url;
}

function confirmSetLocation(message, url){
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}

function deleteConfirm(message, url) {
    confirmSetLocation(message, url);
}

function setElementDisable(element, disable){
    if($(element)){
        $(element).disabled = disable;
    }
}

function toggleParentVis(obj) {
    obj = $(obj).parentNode;
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

// to fix new app/design/adminhtml/default/default/template/widget/form/renderer/fieldset.phtml
// with toggleParentVis
function toggleFieldsetVis(obj) {
    id = obj;
    obj = $(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
    obj = obj.parentNode.childElements();
    for (var i = 0; i < obj.length; i++) {
        if (obj[i].id != undefined 
            && obj[i].id == id 
            && obj[(i-1)].classNames() == 'entry-edit-head') 
        {
            if (obj[i-1].style.display == 'none') {
                obj[i-1].style.display = '';
            } else {
                obj[i-1].style.display = 'none';
            }
        }
    }
}

function toggleVis(obj) {
    obj = $(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function imagePreview(element){
    if($(element)){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
        win.document.close();
        Event.observe(win, 'load', function(){
            var img = win.document.getElementById('image_preview');
            win.resizeTo(img.width+40, img.height+80)
        });
    }
}

function toggleValueElements(checkbox, container){
    if(container && checkbox){
        //var elems = container.getElementsBySelector('select', 'input');
        var elems = Element.getElementsBySelector(container, ['select', 'input', 'textarea']);
        elems.each(function(elem){if(elem!=checkbox)elem.disabled=checkbox.checked});
    }
}

/**
 * @todo add validation for fields
 */
function submitAndReloadArea(area, url) {
    if($(area)) {
        var fields = $(area).getElementsBySelector('input', 'select', 'textarea');
        var data = Form.serializeElements(fields, true);
        new Ajax.Updater(
            area,
            url,
            {
                parameters: $H(data),
                loaderArea: area,
                evalScripts:true
            });
    }
}

/********** MESSAGES ***********/
/*
Event.observe(window, 'load', function() {
    $$('.messages .error-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .warning-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .notice-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E5B82C', endcolor:'#fbf7e9', duration:1});
    });
    $$('.messages .success-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#507477', endcolor:'#f2fafb', duration:1});
    });
});
*/
function syncOnchangeValue(baseElem, distElem){
    var compare = {baseElem:baseElem, distElem:distElem}
    Event.observe(baseElem, 'change', function(){
        if($(this.baseElem) && $(this.distElem)){
            $(this.distElem).value = $(this.baseElem).value;
        }
    }.bind(compare));
}

/********** Ajax session expiration ***********/
