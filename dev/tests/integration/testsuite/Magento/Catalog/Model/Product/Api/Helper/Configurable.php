<?php
/**
 * Helper for configurable product tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Catalog_Model_Product_Api_Helper_Configurable extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve valid configurable data
     *
     * @return array
     */
    public function getValidCreateData()
    {
        require __DIR__ . '/../_files/attribute_set_with_configurable.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var Magento_Eav_Model_Entity_Attribute_Set $attributeSet */
        $attributeSet = Mage::registry('attribute_set_with_configurable');
        /** @var Magento_Catalog_Model_Resource_Eav_Attribute $attributeOne */
        $attributeOne = Mage::registry('eav_configurable_attribute_1');
        /** @var Magento_Catalog_Model_Resource_Eav_Attribute $attributeTwo */
        $attributeTwo = Mage::registry('eav_configurable_attribute_2');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var Magento_Eav_Model_Entity_Attribute_Source_Table $attributeOneSource */
        $attributeOneSource = $attributeOne->getSource();
        $attributeOnePrices = array();
        foreach ($attributeOneSource->getAllOptions(false) as $option) {
            $attributeOnePrices[] = array(
                'option_value' => $option['value'],
                'price' => rand(1, 50),
                'price_type' => rand(0, 1) ? 'percent' : 'fixed' // is percentage used
            );
        }
        $productData['configurable_attributes'] = array(
            array(
                'attribute_code' => $attributeOne->getAttributeCode(),
                'prices' => $attributeOnePrices,
                'frontend_label' => "Must not be used",
                'frontend_label_use_default' => 1,
                'position' => 2
            ),
            array(
                'attribute_code' => $attributeTwo->getAttributeCode(),
                'frontend_label' => "Custom Label",
                'position' => '4'
            )
        );
        return $productData;
    }

    /**
     * Check if the configurable attributes' data was saved correctly during create
     *
     * @param Magento_Catalog_Model_Product $configurable
     * @param array $expectedAttributes
     * @param bool $validatePrices
     */
    public function checkConfigurableAttributesData(
        $configurable,
        $expectedAttributes,
        $validatePrices = true
    ) {
        /** @var Magento_Catalog_Model_Product_Type_Configurable $configurableType */
        $configurableType = $configurable->getTypeInstance();
        $actualAttributes = $configurableType->getConfigurableAttributesAsArray($configurable);
        foreach ($expectedAttributes as $expectedAttribute) {
            $attributeCode = $expectedAttribute['attribute_code'];
            $attributeDataFound = false;
            foreach ($actualAttributes as $actualAttribute) {
                if ($actualAttribute['attribute_code'] == $attributeCode) {
                    $this->_assetAttributes($expectedAttribute, $actualAttribute);
                    if ($validatePrices && isset($expectedAttribute['prices'])
                        && is_array($expectedAttribute['prices'])
                    ) {
                        $this->_assertPrices($actualAttribute['values'], $expectedAttribute['prices']);
                    }
                    $attributeDataFound = true;
                    break;
                }
            }
            $this->assertTrue(
                $attributeDataFound,
                "Attribute with code $attributeCode is not used as a configurable one."
            );
        }
    }

    protected function _assetAttributes($expectedAttribute, $actualAttribute)
    {
        if (isset($expectedAttribute['position'])) {
            $this->assertEquals(
                $expectedAttribute['position'],
                $actualAttribute['position'],
                "Position is invalid."
            );
        }
        if (isset($expectedAttribute['frontend_label_use_default'])
            && $expectedAttribute['frontend_label_use_default'] == 1
        ) {
            $this->assertEquals(
                $expectedAttribute['frontend_label_use_default'],
                $actualAttribute['use_default'],
                "The value of 'use default frontend label' is invalid."
            );
            if (isset($expectedAttribute['frontend_label'])) {
                $this->assertNotEquals(
                    $expectedAttribute['frontend_label'],
                    $actualAttribute['label'],
                    "Default frontend label must be used."
                );
            }
        } else {
            if (isset($expectedAttribute['frontend_label'])) {
                $this->assertEquals(
                    $expectedAttribute['frontend_label'],
                    $actualAttribute['label'],
                    "Frontend label is invalid."
                );
            }
        }
    }

    /**
     * Validate prices
     *
     * @param $actualValues
     * @param $expectedPrices
     */
    protected function _assertPrices($actualValues, $expectedPrices)
    {
        $values = array();
        foreach ($actualValues as $value) {
            $values[$value['value_index']] = $value;
        }
        foreach ($expectedPrices as $expectedValue) {
            if (isset($expectedValue['option_value'])) {
                $this->assertArrayHasKey(
                    $expectedValue['option_value'],
                    $values,
                    'Expected price value not found in actual values.'
                );
                $actualValue = $values[$expectedValue['option_value']];
                if (isset($expectedValue['price'])) {
                    $this->assertEquals(
                        $expectedValue['price'],
                        $actualValue['pricing_value'],
                        'Option price does not match.'
                    );
                }
                if (isset($expectedValue['price_type'])) {
                    $isPercent = ($expectedValue['price_type'] == 'percent') ? 1 : 0;
                    $this->assertEquals(
                        $isPercent,
                        $actualValue['is_percent'],
                        'Option price type does not match.'
                    );
                }
            }
        }
    }

    /**
     * Get valid data for configurable product POST
     *
     * @return array
     */
    protected function _getValidProductPostData()
    {
        return require __DIR__ . '/../_files/_data/product_configurable_all_fields.php';
    }
}
