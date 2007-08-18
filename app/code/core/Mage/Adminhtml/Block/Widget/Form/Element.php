<?php
/**
 * Form element widget block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Form_Element extends Mage_Core_Block_Template
{
    protected $_element;
    protected $_form;
    protected $_formBlock;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('widget/form/element.phtml');
    }
    
    public function setElement($element)
    {
        $this->_element = $element;
        return $this;
    }
    
    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }
    
    public function setFormBlock($formBlock)
    {
        $this->_formBlock = $formBlock;
        return $this;
    }
    
    protected function _beforeToHtml()
    {
        $this->assign('form', $this->_form);
        $this->assign('element', $this->_element);
        $this->assign('formBlock', $this->_formBlock);
        
        return parent::_beforeToHtml();
    }
}
