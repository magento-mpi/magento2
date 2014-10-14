<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Composite;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Configure
 * Adminhtml configurable product composite configure block
 */
class Configure extends \Magento\Catalog\Test\Block\Adminhtml\Product\Composite\Configure
{
    /**
     * Fill options for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        $data = $this->dataMapping($product->getData());
        $this->_fill($data);
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $productOptions = [];
        $checkoutData = $fields['checkout_data']['options'];

        if (!empty($checkoutData['configurable_options'])) {
            $configurableAttributesData = $fields['configurable_attributes_data']['attributes_data'];
            $attributeMapping = parent::dataMapping(['attribute' => '']);
            $selector = $attributeMapping['attribute']['selector'];
            foreach ($checkoutData['configurable_options'] as $key => $optionData) {
                $attribute = $configurableAttributesData[$optionData['title']];
                $attributeMapping['attribute']['selector'] = sprintf($selector, $attribute['label']);
                $attributeMapping['attribute']['value'] = $attribute['options'][$optionData['value']]['label'];
                $productOptions['attribute_' . $key] = $attributeMapping['attribute'];
            }
        }

        return $productOptions;
    }
}
