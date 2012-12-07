<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart model
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Cart_Api_V2 extends Mage_Checkout_Model_Cart_Api
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
