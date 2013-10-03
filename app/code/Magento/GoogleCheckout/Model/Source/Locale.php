<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GoogleCheckout\Model\Source;

class Locale implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'en_US', 'label'=>__('United States')),
            array('value' => 'en_GB', 'label'=>__('United Kingdom')),
        );
    }
}
