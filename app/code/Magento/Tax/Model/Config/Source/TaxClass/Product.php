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

class Product implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        return \Mage::getModel('Magento\Tax\Model\TaxClass\Source\Product')->toOptionArray();
    }
}
