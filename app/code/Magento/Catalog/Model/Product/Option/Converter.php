<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

class Converter
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }


    /**
     * Convert option data to array
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return array
     */
    public function toArray(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option)
    {
        $optionData = $option->getData();
        $values = $option->getData('values');
        $valuesData = [];
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $valuesData[$key] = $value->getData();
            }
        }
        $optionData['values'] = $valuesData;
        return $optionData;
    }
}