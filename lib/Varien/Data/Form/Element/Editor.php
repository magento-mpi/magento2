<?php
/**
 * Form editor element
 *
 * @package    Varien
 * @subpackage Form
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
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
            $html = '
        		<script language="javascript" type="text/javascript" src="'.$this->getForm()->getBaseUrl().'js/tiny_mce/tiny_mce.js"></script>
        		<script language="javascript" type="text/javascript">
                    tinyMCE.init({
                        mode : "exact",
                        theme : "'.$this->getTheme().'",
                        elements : "'.$this->getHtmlId().'",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_path_location : "bottom",
                        extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
                        theme_advanced_resize_horizontal : "false",
                        theme_advanced_resizing : "false",
                        apply_source_formatting : "true",
                        convert_urls : "false",
                        force_br_newlines : "true",
                        doctype : \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">\'
                    });
                </script>
                <textarea name="'.$this->getName().'" title="'.$this->getTitle().'" id="'.$this->getHtmlId().'" class="textarea '.$this->getClass().'" style="width:100%;height:300px">'.$this->getEscapedValue().'</textarea>';

                /*plugins : "inlinepopups,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
                theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,|,visualchars,nonbreaking"*/
            
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
}
