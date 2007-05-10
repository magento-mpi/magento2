<?php
/**
 * Hidden Form element block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Hidden extends Mage_Core_Block_Abstract 
{
    public function __construct($attributes) 
    {
        $attributes['type'] = 'hidden';
        parent::__construct($attributes);
    }
    
    function toHtml()
    {
        if (!$this->getName()) {
            Mage::exception('Hidden form element must have "name" attribute');
        }
        
        $html = '<input type="hidden" ';
        $html.= $this->_attributesToString(array('name', 'id', 'value', 'class'));
        $html.= '/>';
        return $html;
    }
}