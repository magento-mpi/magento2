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
namespace Magento\Checkout\Model\Cart\Api;

class V2 extends \Magento\Checkout\Model\Cart\Api
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
