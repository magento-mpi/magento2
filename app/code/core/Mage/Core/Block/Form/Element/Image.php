<?php

/**
 * Form input type="image" block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Image extends Mage_Core_Block_Form_Element_Abstract 
{
    public public function __construct($attributes) 
    {
        parent::__construct($attributes);
    }
    
    public function toHtml()
    {
        $html = $this->renderElementLabel();
        $html.= '<input type="image" ';
        $html.= $this->_attributesToString(array(
                'name'
               ,'id'
               ,'value'
               ,'src'
               ,'title'
               ,'accesskey'
               ,'tabindex'
               ,'class'
               ,'style'
               ,'disabled'
               ,'onclick'
               ,'onchange'
               ,'onselect'
               ,'onfocus'
               ,'onblur'
               ,'ext_type'));

        $html.= '/>';
        
        return $html;
    }
}