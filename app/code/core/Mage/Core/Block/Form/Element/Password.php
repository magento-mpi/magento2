<?php

/**
 * Form input type="password" block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Password extends Mage_Core_Block_Form_Element_Abstract 
{
    public public function __construct($attributes) 
    {
        parent::__construct($attributes);
    }
    
    public function toHtml()
    {
        $html = $this->renderElementLabel();
        $html.= '<input type="password" ';
        $html.= $this->_attributesToString(array(
                        'name'
                       ,'id'
                       ,'value'
                       ,'title'
                       ,'maxlength'
                       ,'size'
                       ,'accesskey'
                       ,'tabindex'
                       ,'class'
                       ,'style'
                       ,'disabled'
                       ,'readonly'
                       ,'onclick'
                       ,'onchange'
                       ,'onselect'
                       ,'onfocus'
                       ,'onblur'));

        $html.= '/>';
        
        return $html;
    }
}