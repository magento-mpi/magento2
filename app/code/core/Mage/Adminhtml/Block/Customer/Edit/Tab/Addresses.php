<?php
/**
 * Custmer addresses forms
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/tab/addresses.phtml');
    }
    
    public function getRegionsUrl()
    {
        return Mage::getUrl('directory/json/childRegion');
    }
    
    protected function _beforeToHtml()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('address_fieldset', array('legend'=>__('edit customer address')));
        
        $addressModel = Mage::getModel('customer/address');
        
        $this->_setFieldset($addressModel->getAttributes(), $fieldset);

        if ($regionElement = $form->getElement('region')) {
            $regionElement->setRenderer(Mage::getModel('adminhtml/customer_renderer_region'));
        }
        
        $addressCollection = Mage::registry('customer')->getLoadedAddressCollection();
        $this->assign('customer', Mage::registry('customer'));
        $this->assign('addressCollection', $addressCollection);
        //$addressCollection->loadByCustomerId($customerId);
        $this->setForm($form);

        return parent::_beforeToHtml();
    }
}
