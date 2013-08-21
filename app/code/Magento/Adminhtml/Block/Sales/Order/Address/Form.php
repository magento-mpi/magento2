<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml sales order edit address block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Address_Form
    extends Magento_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    protected $_template = 'sales/order/address/form.phtml';

    /**
     * Order address getter
     *
     * @return Magento_Sales_Model_Order_Address
     */
    protected function _getAddress()
    {
        return Mage::registry('order_address');
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Billing_Address
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction($this->getUrl('*/*/addressSave', array('address_id'=>$this->_getAddress()->getId())));
        $this->_form->setUseContainer(true);
        return $this;
    }

    /**
     * Form header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Order Address Information');
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_getAddress()->getData();
    }
}
