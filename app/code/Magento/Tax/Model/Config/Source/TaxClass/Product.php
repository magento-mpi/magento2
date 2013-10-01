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
     * @var \Magento\Tax\Model\ClassModel\Source\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Tax\Model\ClassModel\Source\ProductFactory $productFactory
     */
    public function __construct(\Magento\Tax\Model\ClassModel\Source\ProductFactory $productFactory)
    {
        $this->_productFactory = $productFactory;
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $sourceProduct \Magento\Tax\Model\ClassModel\Source\Product */
        $sourceProduct = $this->_productFactory->create();
        return $sourceProduct->toOptionArray();
    }
}
