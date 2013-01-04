<?php
/**
 * Helper for configurable product tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Model_Product_Api_Helper_Configurable extends PHPUnit_Framework_TestCase
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
        /** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
        $attributeSet = Mage::registry('attribute_set_with_configurable');
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeOne */
        $attributeOne = Mage::registry('eav_configurable_attribute_1');
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeTwo */
        $attributeTwo = Mage::registry('eav_configurable_attribute_2');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var Mage_Eav_Model_Entity_Attribute_Source_Table $attributeOneSource */
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
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $expectedConfigurableData
     * @param bool $validatePrices
     */
    public function checkConfigurableAttributesData(
        $configurable,
        $expectedConfigurableData,
        $validatePrices = true
    ) {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $configurableType */
        $configurableType = $configurable->getTypeInstance();
        $actualConfigurableData = $configurableType->getConfigurableAttributesAsArray($configurable);
        foreach ($expectedConfigurableData as $expectedData) {
            $attributeCode = $expectedData['attribute_code'];
            $attributeDataFound = false;
            foreach ($actualConfigurableData as $actualData) {
                if ($actualData['attribute_code'] == $attributeCode) {
                    if (isset($expectedData['position'])) {
                        $this->assertEquals($expectedData['position'], $actualData['position'], "Position is invalid.");
                    }
                    if (isset($expectedData['frontend_label_use_default'])
                        && $expectedData['frontend_label_use_default'] == 1
                    ) {
                        $this->assertEquals(
                            $expectedData['frontend_label_use_default'],
                            $actualData['use_default'],
                            "The value of 'use default frontend label' is invalid."
                        );
                        if (isset($expectedData['frontend_label'])) {
                            $this->assertNotEquals(
                                $expectedData['frontend_label'],
                                $actualData['label'],
                                "Default frontend label must be used."
                            );
                        }
                    } else {
                        if (isset($expectedData['frontend_label'])) {
                            $this->assertEquals(
                                $expectedData['frontend_label'],
                                $actualData['label'],
                                "Frontend label is invalid."
                            );
                        }
                    }
                    if ($validatePrices && isset($expectedData['prices']) && is_array($expectedData['prices'])) {
                        $values = array();
                        foreach ($actualData['values'] as $value) {
                            $values[$value['value_index']] = $value;
                        }
                        foreach ($expectedData['prices'] as $expectedValue) {
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
