<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductForm;

/**
 * Class AssertConfigurableProductForm
 */
class AssertConfigurableProductForm extends AssertProductForm
{
    /**
     * List skipped fixture fields in verify
     *
     * @var array
     */
    protected $skippedFixtureFields = [
        'affected_attribute_set'
    ];

    /**
     * List skipped attribute fields in verify
     *
     * @var array
     */
    protected $skippedAttributeFields = [
        'frontend_input',
        'attribute_code',
        'attribute_id',
        'is_required',
    ];

    /**
     * List skipped option fields in verify
     *
     * @var array
     */
    protected $skippedOptionFields = [
        'id',
        'is_default',
    ];

    protected $skippedVariationMatrixFields = [
        'configurable_attribute'
    ];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Prepares fixture data for comparison
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFixtureData(array $data, array $sortFields = [])
    {
        $data = array_diff_key($data, array_flip($this->skippedFixtureFields));

        // filter values and reset keys in attributes data
        $attributeData = $data['configurable_attributes_data']['attributes_data'];
        foreach ($attributeData as $attributeKey => $attribute) {
            foreach ($attribute['options'] as $optionKey => $option) {
                $attribute['options'][$optionKey] = array_diff_key($option, array_flip($this->skippedOptionFields));
            }
            $attribute['options'] = array_values($attribute['options']);
            $attributeData[$attributeKey] = array_diff_key($attribute, array_flip($this->skippedAttributeFields));
        }
        $data['configurable_attributes_data']['attributes_data'] = array_values($attributeData);


        // filter values and reset keys in variation matrix
        $variationsMatrix = $data['configurable_attributes_data']['matrix'];
        foreach ($variationsMatrix as $key => $variationMatrix) {
            $variationsMatrix[$key] = array_diff_key($variationMatrix, array_flip($this->skippedVariationMatrixFields));
        }
        $data['configurable_attributes_data']['matrix'] = array_values($variationsMatrix);

        return parent::prepareFixtureData($data, $sortFields);
    }

    /**
     * Prepares form data for comparison
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFormData(array $data, array $sortFields = [])
    {
        // filter values and reset keys in variation matrix
        $variationsMatrix = $data['configurable_attributes_data']['matrix'];
        foreach ($variationsMatrix as $key => $variationMatrix) {
            $variationsMatrix[$key] = array_diff_key($variationMatrix, array_flip($this->skippedVariationMatrixFields));
        }
        $data['configurable_attributes_data']['matrix'] = array_values($variationsMatrix);

        foreach ($sortFields as $path) {
            $data = $this->sortDataByPath($data, $path);
        }
        return $data;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
