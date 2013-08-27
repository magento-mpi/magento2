<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart model
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Cart_Api_V2 extends Magento_Checkout_Model_Cart_Api
{
    /**
     * Prepare payment data for further usage
     *
     * @param array $data
     * @return array
     */
    protected function _preparePaymentData($data)
    {
        $data = get_object_vars($data);
        return parent::_preparePaymentData($data);
    }
}
