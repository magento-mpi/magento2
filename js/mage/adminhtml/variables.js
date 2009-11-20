/**
 * Magento
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the file LICENSE_AFL.txt. It is also available
 * through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php If you did not receive a copy of
 * the license and are unable to obtain it through the world-wide-web, please
 * send an email to license@magentocommerce.com so we can send you a copy
 * immediately.
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your needs
 * please refer to http://www.magentocommerce.com for more information.
 * 
 * @copyright Copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *            (http://www.varien.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License
 *          (AFL 3.0)
 */

var Variables = {
    textareaElementId: null,
    variablesContent: null,
    dialogWindow: null,
    overlayShowEffectOptions: null,
    overlayHideEffectOptions: null,
    insertFunction: 'Variables.insertVariable',
    init: function(textareaElementId, insertFunction) {
        if ($(textareaElementId)) {
            this.textareaElementId = textareaElementId;
        }
        if (insertFunction) {
            this.insertFunction = insertFunction;
        }
    },
    
    resetData: function() {
        this.variablesContent = null;
        this.dialogWindow = null;
    },
    
    openVariableChooser: function(variables) {
        if (this.variablesContent == null && variables) {
            this.variablesContent = '<ul>';
            variables.each(function(variableGroup) {
                this.variablesContent += '<li><b>' + variableGroup.label + '</b></li>';
                (variableGroup.value).each(function(variable){
                    this.variablesContent += '<li style="padding-left: 20px;">' + 
                        this.prepareVariableRow(variable.value, variable.label) + '</li>';
                }.bind(this));
            }.bind(this));
            this.variablesContent += '</ul>';
        }
        if (this.variablesContent) {
            this.openDialogWindow(this.variablesContent);
        }
    },
    openDialogWindow: function(variablesContent) {
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration:0};
        Windows.overlayHideEffectOptions = {duration:0};

        this.dialogWindow = Dialog.info(variablesContent, {
            draggable:true,
            resizable:true,
            closable:true,
            className:"magento",
            title:'Insert Variable...',
            width:700,
            height:500,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"variables-chooser",
            onClose: this.closeDialogWindow.bind(this)
        });
        variablesContent.evalScripts.bind(variablesContent).defer();
    },
    closeDialogWindow: function(window) {
        if (!window) {
            window = this.dialogWindow;
        }
        if (window) {
            window.close();
            Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
            Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
        }
    },
    prepareVariableRow: function(varValue, varLabel) {
        var value = (varValue).replace(/"/g, '&quot;').replace(/'/g, '\\&#39;');
        var content = '<a href="javascript:void();" onclick="'+this.insertFunction+'(\''+ value +'\');">' + varLabel + '</a>';
        return content;
    },
    insertVariable: function(value) {
        var textareaElm = $(this.textareaElementId);
        if (textareaElm) {
            var scrollPos = textareaElm.scrollTop;
            var strPos = textareaElm.selectionStart;

            var front = (textareaElm.value).substring(0, strPos);
            var back = (textareaElm.value).substring(strPos, textareaElm.value.length);

            strPos = strPos + (value).length;
        
            textareaElm.value = front + value + back;
            textareaElm.selectionStart = strPos;
            textareaElm.selectionEnd = strPos;
            textareaElm.focus();
            textareaElm.scrollTop = scrollPos;
            textareaElm = null;
        }
        this.closeDialogWindow(this.dialogWindow);
    }
};
