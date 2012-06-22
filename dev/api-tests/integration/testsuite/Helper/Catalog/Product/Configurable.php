<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Helper_Catalog_Product_Configurable extends Magento_Test_Webservice
{
    /**
     * Retrieve valid configurable data
     *
     * @return array
     */
    public function getValidCreateData()
    {
        require TEST_FIXTURE_DIR . '/Catalog/Product/Configurable/attribute_set.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
        $attributeSet = $this->getFixture('attribute_set_with_configurable');
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeOne */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attributeTwo */
        $attributeTwo = $this->getFixture('eav_configurable_attribute_2');
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
     * Retrieve product data with attribute set not suitable for the configurable product creation
     *
     * @return array
     */
    public function getCreateDataWithInvalidAttributeSet()
    {
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $productData['attribute_set_id'] = $entityType->getDefaultAttributeSetId();
        return $productData;
    }

    /**
     * Retrieve product data with configurable attribute that cannot be used for the configurable product creation
     *
     * @return array
     */
    public function getCreateDataWithInvalidConfigurableAttribute()
    {
        require TEST_FIXTURE_DIR . '/Catalog/Product/Configurable/attribute_set_with_invalid_attribute.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = $this->getFixture('attribute_set_with_invalid_attribute');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var $invalidAttribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $invalidAttribute = $this->getFixture('eav_invalid_configurable_attribute');
        $productData['configurable_attributes'] = array(
            array('attribute_code' => $invalidAttribute->getAttributeCode()),
            array('attribute_code' => 'NOT_EXISTING_ATTRIBUTE')
        );
        return $productData;
    }

    /**
     * Retrieve product data with invalid configurable option price
     *
     * @return array
     */
    public function getCreateDataWithInvalidConfigurableOptionPrice()
    {
        require TEST_FIXTURE_DIR . '/Catalog/Product/Configurable/attribute_set_with_one_attribute.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = $this->getFixture('attribute_set_with_one_attribute');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getFixture('eav_configurable_attribute');
        $attributeSourceOptions = $attribute->getSource()->getAllOptions(false);
        $productData['configurable_attributes'] = array(array(
            'attribute_code' => $attribute->getAttributeCode(),
            'frontend_label' => $attribute->getFrontendLabel(),
            'prices' => array(
                array(
                    'option_value' => $attributeSourceOptions[0]['value'],
                    'price' => 'invalid',
                    'price_type' => 'invalid!@#~%^&*'
                ),
                array(
                    'option_value' => $attributeSourceOptions[1]['value'],
                    'price_type' => 'fixed',
                    'price' => rand(1, 100)
                )
            )
        ));
        return $productData;
    }

    /**
     * Retrieve product data with invalid configurable option value
     *
     * @return array
     */
    public function getCreateDataWithInvalidConfigurableOptionValue()
    {
        require TEST_FIXTURE_DIR . '/Catalog/Product/Configurable/attribute_set_with_one_attribute.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = $this->getFixture('attribute_set_with_one_attribute');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getFixture('eav_configurable_attribute');
        $productData['configurable_attributes'] = array(array(
            'attribute_code' => $attribute->getAttributeCode(),
            'frontend_label' => $attribute->getFrontendLabel(),
            'prices' => array(
                array(
                    'option_value' => 'invalid_option_value',
                    'price_type' => 'fixed',
                    'price' => rand(1, 100)
                ),
            )
        ));
        return $productData;
    }

    /**
     * Retrieve product data with invalid configurable option label
     *
     * @return array
     */
    public function getCreateDataWithInvalidConfigurableOptionLabel()
    {
        require TEST_FIXTURE_DIR . '/Catalog/Product/Configurable/attribute_set.php';
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = $this->getFixture('attribute_set_with_configurable');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var $attributeOne Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        /** @var $attributeTwo Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeTwo = $this->getFixture('eav_configurable_attribute_2');
        $productData['configurable_attributes'] = array(
            array(
                'attribute_code' => $attributeOne->getAttributeCode(),
                'frontend_label' => '  ',
            ),
            array(
                'attribute_code' => $attributeTwo->getAttributeCode(),
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
    public function checkConfigurableAttributesData($configurable, $expectedConfigurableData,
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
                        && $expectedData['frontend_label_use_default'] == 1) {
                        $this->assertEquals($expectedData['frontend_label_use_default'], $actualData['use_default'],
                            "The value of 'use default frontend label' is invalid.");
                        if (isset($expectedData['frontend_label'])) {
                            $this->assertNotEquals($expectedData['frontend_label'], $actualData['label'],
                                "Default frontend label must be used.");
                        }
                    } else {
                        if (isset($expectedData['frontend_label'])) {
                            $this->assertEquals($expectedData['frontend_label'], $actualData['label'],
                                "Frontend label is invalid.");
                        }
                    }
                    if ($validatePrices && isset($expectedData['prices']) && is_array($expectedData['prices'])) {
                        $values = array();
                        foreach ($actualData['values'] as $value) {
                            $values[$value['value_index']] = $value;
                        }
                        foreach ($expectedData['prices'] as $expectedValue) {
                            if (isset($expectedValue['option_value'])) {
                                $this->assertArrayHasKey($expectedValue['option_value'], $values,
                                    'Expected price value not found in actual values.');
                                $actualValue = $values[$expectedValue['option_value']];
                                if (isset($expectedValue['price'])) {
                                    $this->assertEquals($expectedValue['price'], $actualValue['pricing_value'],
                                        'Option price does not match.');
                                }
                                if (isset($expectedValue['price_type'])) {
                                    $isPercent = ($expectedValue['price_type'] == 'percent') ? 1 : 0;
                                    $this->assertEquals($isPercent, $actualValue['is_percent'],
                                        'Option price type does not match.');
                                }
                            }
                        }
                    }
                    $attributeDataFound = true;
                    break;
                }
            }
            $this->assertTrue($attributeDataFound,
                "Attribute with code $attributeCode is not used as a configurable one.");
        }
    }

    /**
     * Get valid data for configurable product POST
     *
     * @return array
     */
    protected function _getValidProductPostData()
    {
        return require TEST_FIXTURE_DIR . '/_data/Catalog/Product/Configurable/product_configurable_all_fields.php';
    }
}
