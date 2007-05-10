<?php



/**
 * Form input type="text" block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Text extends Mage_Core_Block_Form_Element_Abstract 
{
    public public function __construct($attributes) 
    {
        $attributes['type'] = 'text';
        parent::__construct($attributes);
    }
    
    public function toHtml()
    {
        $html = $this->renderElementLabel();
        if(!$this->getClass()){
            $this->setClass('x-form-field x-form-text');
        }
        $html.= '<input type="text" ';
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
                       ,'onblur'
                       ,'ext_type'));
        $html.= '/>';
        
        return $html;
    }
}