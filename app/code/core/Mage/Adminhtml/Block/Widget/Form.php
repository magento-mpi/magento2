<?php
/**
 * Form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Form extends Mage_Adminhtml_Block_Widget 
{
    protected $_form;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/widget/form.phtml');
    }
    
    public function getForm()
    {
        return $this->_form;
    }
    
    public function setForm(Varien_Data_Form $form)
    {
        $this->_form = $form;
    }
    
    public function drawElement(Varien_Data_Form_Abstract $element)
    {
        return $this->getLayout()->createBlock('adminhtml/widget_form_element')
            ->setForm($this->getForm())
            ->setElement($element)
            ->setFormBlock($this)
            ->toHtml();
    }
    
    protected function _beforeToHtml()
    {
        $this->assign('form', $this->getForm());
        return $this;
    }
}