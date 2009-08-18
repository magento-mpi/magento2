<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form editor element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Editor extends Varien_Data_Form_Element_Textarea
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        if( $this->getWysiwyg() === true )
        {
            $this->setType('wysiwyg');
            $this->setExtType('wysiwyg');
        }
        else
        {
            $this->setType('textarea');
            $this->setExtType('textarea');
        }
    }

    public function getElementHtml()
    {
        if( $this->getWysiwyg() === true )
        {
            $config = $this->getConfig();
            $element = ($this->getState() == 'html') ? '' : $this->getHtmlId();

            $html = '
                <script type="text/javascript" src="'.$this->getForm()->getParent()->getJsUrl().'tiny_mce/tiny_mce.js" ></script>
        		<script type="text/javascript">
				//<![CDATA[

                function imagesBrowser(field_name, url, type, win) {
                    win.open("'.$config->getFilesBrowserWindowUrl().'", "imagesBrowser", "width='.$config->getFilesBrowserWindowWidth().', height='.$config->getFilesBrowserWindowHeight().'");
                }

                function toggleTiny(id) {
                    if (!tinyMCE.get(id)) {
                        setupEditor();
                        setTimeout("",1000);
                        tinyMCE.execCommand("mceAddControl", false, id);
                    } else {
                        tinyMCE.execCommand("mceRemoveControl", false, id);
                    }
                }

                function onFormValidation'.$this->getHtmlId().'(formId) {
                    if (tinyMCE.get("'.$this->getHtmlId().'")) {
                        $("'.$this->getHtmlId().'").value = tinyMCE.get("'.$this->getHtmlId().'").getContent();
                    }
                }

                function setupEditor() {
                    tinymce.PluginManager.load("magentowidget", "'.Mage::getBaseUrl('js').'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentowidget/editor_plugin.js");
                    tinyMCE.init({
                        mode : "exact",
                        elements : "'.$this->getHtmlId().'",
                        theme : "advanced",
                        plugins : "-magentowidget,inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
                        theme_advanced_buttons1 : "magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
                        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,
                        file_browser_callback : "imagesBrowser",
                        convert_urls : false,
                        relative_urls : false,
                        content_css: "",
                        magentowidget_url: "' . $config->getWidgetWindowUrl() . '",
                        doctype : \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">\',

                        setup : function(ed) {
                            ed.onSubmit.add(function(ed, e) {
                                varienGlobalEvents.fireEvent("tinymceSubmit", e);
                            });

                            ed.onPaste.add(function(ed, e, o) {
                                varienGlobalEvents.fireEvent("tinymcePaste", o);
                            });

                            ed.onBeforeSetContent.add(function(ed, o) {
                                varienGlobalEvents.fireEvent("tinymceBeforeSetContent", o);
                            });

                            ed.onSetContent.add(function(ed, o) {
                                varienGlobalEvents.fireEvent("tinymceSetContent", o);
                            });

                            ed.onSaveContent.add(function(ed, o) {
                                varienGlobalEvents.fireEvent("tinymceSaveContent", o);
                            });

                            ed.onChange.add(function(ed, l) {
                                varienGlobalEvents.fireEvent("tinymceChange", l);
                            });

                            ed.onExecCommand.add(function(ed, cmd, ui, val) {
                                varienGlobalEvents.fireEvent("tinymceExecCommand", cmd);
                            });
                        }
                    });

                }

                '.($config->getEnabled() ? 'Event.observe(window, "load", function() { setupEditor(); });' : '').'

                varienGlobalEvents.attachEventHandler("formSubmit", onFormValidation'.$this->getHtmlId().');

				//]]>
                </script>
                <textarea name="'.$this->getName().'" title="'.$this->getTitle().'" id="'.$this->getHtmlId().'" class="textarea '.$this->getClass().'" '.$this->serialize($this->getHtmlAttributes()).' >'.$this->getEscapedValue().'</textarea>
                <a href="javascript:toggleTiny(\''.$this->getHtmlId().'\');">'.($config->getToggleLinkTitle() ? $config->getToggleLinkTitle() : 'Add/Remove Editor').'</a>';


            if ($this->getConfig()->getEncodeDirectives() && $this->getConfig()->getDirectivesUrl()) {

                $directivesUrl = $this->getConfig()->getDirectivesUrl();
                $directivesUrlQuoted = preg_quote($directivesUrl, '/');

                $html .= '
                    <script type="text/javascript">
    				//<![CDATA[

    				function encodeDirectives(content) {
                        return content.gsub(/([a-z]+)\s*\=\s*[\"\']{1}(\{\{[a-z]{0,10}.*?\}\})[\"\']{1}/i, function(match){
                            return match[1] + "=\"'.$directivesUrl.'directive/" + Base64.mageEncode(match[2]) + "/\"";
                        });
                    }

                    function decodeDirectives(content) {
                        return content.gsub(/'.$directivesUrlQuoted.'directive\/([a-zA-Z0-9\-\_\,]+)\/?/i, function(match){
                            return Base64.mageDecode(match[1]);
                        });
                    }

                    function BeforeSetContent'.$this->getHtmlId().' (o) {
                        o.content = encodeDirectives(o.content);
                    }

                    function SaveContent'.$this->getHtmlId().' (o) {
                        o.content = decodeDirectives(o.content);
                    }

                    varienGlobalEvents.attachEventHandler("tinymceBeforeSetContent", BeforeSetContent'.$this->getHtmlId().');
                    varienGlobalEvents.attachEventHandler("tinymceSaveContent", SaveContent'.$this->getHtmlId().');

    				//]]>
                    </script>';
            }

            $html.= $this->getAfterElementHtml();
            return $html;
        }
        else
        {
            return parent::getElementHtml();
        }
    }

    public function getTheme()
    {
        if(!$this->hasData('theme')) {
            return 'simple';
        }

        return $this->getData('theme');
    }

    /**
     * Editor config retriever
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if ( !($this->getData('config') instanceof Varien_Object) ) {
            $config = new Varien_Object();
            $this->setConfig($config);
        }
        return $this->getData('config');
    }
}
