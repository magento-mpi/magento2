<?php
/**
 * Customer edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Widget 
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/edit.phtml');
        $this->setId('customerEdit');
    }
    
    protected function _initChildren()
    {
        $this->setChild('saveButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save customer'),
                    'onclick'   => 'customerForm.submit()'
                ))
        );
        $this->setChild('deleteButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete customer'),
                    'onclick'   => 'customerDelete()'
                ))
        );
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }
    
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getCustomerId()
    {
        return Mage::registry('customer')->getId();
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }
    
    public function getHeader()
    {
        if (Mage::registry('customer')->getId()) {
            return Mage::registry('customer')->getName();
        }
        else {
            return __('New Customer');
        }
    }
}
