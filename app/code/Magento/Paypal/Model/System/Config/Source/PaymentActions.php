<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available payment actions
 */
namespace Magento\Paypal\Model\System\Config\Source;

class PaymentActions implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = \Mage::getModel('Magento\Paypal\Model\Config');
        return $configModel->getPaymentActions();
    }
}
