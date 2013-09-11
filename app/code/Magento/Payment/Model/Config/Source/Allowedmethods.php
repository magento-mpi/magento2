<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Config\Source;

class Allowedmethods
    extends \Magento\Payment\Model\Config\Source\Allmethods
{
    protected function _getPaymentMethods()
    {
        return \Mage::getSingleton('Magento\Payment\Model\Config')->getActiveMethods();
    }
}
