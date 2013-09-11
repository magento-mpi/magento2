<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Config\Source\TaxClass;

class Customer implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve a list of customer tax classes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $taxClasses = \Mage::getModel('\Magento\Tax\Model\TaxClass\Source\Customer')->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
