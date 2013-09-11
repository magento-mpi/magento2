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
 * Source model for available paypal express payment actions
 */
namespace Magento\Paypal\Model\System\Config\Source\PaymentActions;

class Express
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configModel = \Mage::getModel('\Magento\Paypal\Model\Config');
        $configModel->setMethod(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
