<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request Details Block at RMA page
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingaddress
    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{
    /**
     * Get order shipping address
     *
     * @return string|null
     */
    public function getOrderShippingAddress()
    {
        $address = $this->getOrder()->getShippingAddress();
        if ($address instanceof Magento_Sales_Model_Order_Address) {
            return $address->format('html');
        } else {
            return null;
        }
    }
}
