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
 * Shopping cart api
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

 class Magento_Checkout_Model_Cart_Payment_Api_V2 extends Magento_Checkout_Model_Cart_Payment_Api
{
     protected function _preparePaymentData($data)
     {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_preparePaymentData($_data);
        }

        return array();
     }
}
