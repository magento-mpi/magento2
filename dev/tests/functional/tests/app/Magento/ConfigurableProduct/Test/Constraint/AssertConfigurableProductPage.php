<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductPage;

/**
 * Class AssertConfigurableProductPage
 * Assert that displayed product data on product page(front-end) equals passed from fixture:
 * 1. Product Name
 * 2. Price
 * 3. SKU
 * 4. Description
 * 5. Short Description
 * 6. Attributes
 */
class AssertConfigurableProductPage extends AssertProductPage
{
    /**
     * Verify displayed product data on product page(front-end) equals passed from fixture
     *
     * @return array
     */
    protected function verify()
    {
        $errors = parent::verify();
        $errors[] = $this->verifyAttributes();

        return array_filter($errors);
    }

    /**
     * Verify displayed product attributes on product page(front-end) equals passed from fixture
     *
     * @return string|null
     */
    protected function verifyAttributes()
    {
        $attributesData = $this->product->getConfigurableAttributesData()['attributes_data'];
        $configurableOptions = [];
        $formOptions = $this->productView->getOptions($this->product)['configurable_options'];

        foreach ($attributesData as $attributeKey => $attributeData) {
            $optionData = [
                'title' => $attributeData['frontend_label'],
                'type' => $attributeData['frontend_input'],
                'is_require' => 'Yes',
            ];

            foreach ($attributeData['options'] as $optionKey => $option) {
                $price = ('Yes' == $option['is_percent'])
                    ? ($this->product->getPrice() * $option['pricing_value']) / 100
                    : $option['pricing_value'];

                $optionData['options'][$optionKey] = [
                    'title' => $option['label'],
                    'price' => number_format($price, 2)
                ];
            }

            $configurableOptions[$attributeKey] = $optionData;
        }

        // Sort data for compare
        $configurableOptions = $this->sortDataByPath($configurableOptions, '::title');
        foreach ($configurableOptions as $key => $configurableOption) {
            $configurableOptions[$key] = $this->sortDataByPath($configurableOption, 'options::title');
        }
        $formOptions = $this->sortDataByPath($formOptions, '::title');
        foreach ($formOptions as $key => $formOption) {
            $formOptions[$key] = $this->sortDataByPath($formOption, 'options::title');
        }

        $errors = $this->verifyData($configurableOptions, $formOptions, true, false);
        return empty($errors) ? null : $this->prepareErrors($errors, 'Error configurable options:');
    }
}
