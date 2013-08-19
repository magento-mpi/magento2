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
 * Adminhtml sales order create billing address block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Billing_Address
    extends Magento_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    /**
     * Return header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Billing Address');
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-billing-address';
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Billing_Address
     */
    protected function _prepareForm()
    {
        $this->setJsVariablePrefix('billingAddress');
        parent::_prepareForm();

        $this->_form->addFieldNameSuffix('order[billing_address]');
        $this->_form->setHtmlNamePrefix('order[billing_address]');
        $this->_form->setHtmlIdPrefix('order-billing_address_');

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->getCreateOrderModel()->getBillingAddress()->getData();
    }

    /**
     * Return customer address id
     *
     * @return int|boolean
     */
    public function getAddressId()
    {
        return $this->getCreateOrderModel()->getBillingAddress()->getCustomerAddressId();
    }

    /**
     * Return billing address object
     *
     * @return Magento_Customer_Model_Address
     */
    public function getAddress()
    {
        return $this->getCreateOrderModel()->getBillingAddress();
    }
}
