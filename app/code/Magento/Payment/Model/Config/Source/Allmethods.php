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

class Allmethods implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $methods = \Mage::helper('Magento\Payment\Helper\Data')->getPaymentMethodList(true, true, true);
        return $methods;
    }
}
