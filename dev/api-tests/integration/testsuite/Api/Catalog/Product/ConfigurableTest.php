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

/**
 * Test configurable product API
 */
class Api_Catalog_Product_ConfigurableTest extends Magento_Test_Webservice
{
    /**
     * @var Helper_Catalog_Product_Configurable
     */
    static protected $_configurableHelper;

    /**
     * Initialize helpers
     */
    public static function setUpBeforeClass()
    {
        self::$_configurableHelper = new Helper_Catalog_Product_Configurable();
        parent::setUpBeforeClass();
    }

    /**
     * Test successful configurable product create.
     * Scenario:
     * 1. Create EAV attributes and attribute set usable for configurable.
     * 2. Send request to create product with type 'configurable' and all valid attributes data.
     * Expected result:
     * Load product and assert it was created correctly.
     */
    public function testCreate()
    {
        $productData = self::$_configurableHelper->getValidCreateData();
        $productId = $this->_createProductWithApi($productData);
        // Validate outcome
        /** @var $actual Mage_Catalog_Model_Product */
        $actual = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
        $this->addModelToDelete($actual, true);
        self::$_configurableHelper->checkConfigurableAttributesData($actual, $productData['configurable_attributes'],
            false);
        unset($productData['configurable_attributes']);
        $expected = new Mage_Catalog_Model_Product();
        $expected->setData($productData);
        $this->assertProductEquals($expected, $actual);
    }

    /**
     * Test configurable product create pre-validation.
     * Scenario:
     * 1. Send request for product create with type 'configurable' and default attribute set which does not contain
     *    any configurable attributes by default.
     * Expected result:
     * Assert that correct error message was returned in the response.
     */
    public function testCreateInvalidAttributeSet()
    {
        $productData = self::$_configurableHelper->getCreateDataWithInvalidAttributeSet();
        $expectedMessage = "The specified attribute set does not contain attributes which "
            . "can be used for the configurable product.";
        $this->_createProductWithErrorMessagesCheck($productData, $expectedMessage);
    }

    /**
     * Test configurable product create pre-validation.
     * Scenario:
     * 1. Create attributes:
     *   'valid_attribute' with scope "Global", input type "Dropdown", and Use To Create Configurable Product "Yes".
     *   'invalid_attribute' with scope "Global", input type "Dropdown".
     * 2. Create an Attribute Set and add attributes from step 1.
     * 3. Try to create Configurable with Attr. Set from step 2.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     */
    public function testCreateInvalidAttribute()
    {
        $productData = self::$_configurableHelper->getCreateDataWithInvalidConfigurableAttribute();
        /** @var $invalidAttribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $invalidAttribute = $this->getFixture('eav_invalid_configurable_attribute');
        $expectedMessages = array(
            sprintf('The attribute with code "%s" cannot be used to create a configurable product.',
                $invalidAttribute->getAttributeCode()),
            'The attribute with code "NOT_EXISTING_ATTRIBUTE" cannot be used to create a configurable product.'
        );
        $this->_createProductWithErrorMessagesCheck($productData, $expectedMessages);
    }

    /**
     * Test configurable product create pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" with 2 options values: 1 and 2.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify invalid price and invalid price type in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     */
    public function testCreateInvalidAttributePrice()
    {
        $productData = self::$_configurableHelper->getCreateDataWithInvalidConfigurableOptionPrice();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getFixture('eav_configurable_attribute');
        $attributeSourceOptions = $attribute->getSource()->getAllOptions(false);
        $expectedMessages = array(
            sprintf('The "price" value for the option value "%s" in the "prices" '
                    . 'array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[0]['value'], $attribute->getAttributeCode()),
            sprintf('The "price_type" value for the option value "%s" in the '
                    . '"prices" array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[0]['value'], $attribute->getAttributeCode()),
            sprintf('The "price_type" value for the option value "%s" in the '
                    . '"prices" array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[1]['value'], $attribute->getAttributeCode())
        );
        $this->_createProductWithErrorMessagesCheck($productData, $expectedMessages);
    }

    /**
     * Test configurable product create pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" without any options.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify invalid option_value in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     */
    public function testCreateInvalidAttributeOptionValue()
    {
        $productData = self::$_configurableHelper->getCreateDataWithInvalidConfigurableOptionValue();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getFixture('eav_configurable_attribute');
        // Validate outcome
        $expectedMessages = array(
            "The \"option_value\" value \"invalid_option_value\" for the configurable attribute"
                . " with code \"{$attribute->getAttributeCode()}\" is invalid."
        );
        $this->_createProductWithErrorMessagesCheck($productData, $expectedMessages);
    }

    /**
     * Test configurable product create pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" without any options.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify empty frontend label for one attribute
     *    and do not specify frontend label for second attribute in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     */
    public function testCreateInvalidFrontendLabel()
    {
        $productData = self::$_configurableHelper->getCreateDataWithInvalidConfigurableOptionLabel();
        /** @var $attributeOne Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        /** @var $attributeTwo Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeTwo = $this->getFixture('eav_configurable_attribute_2');
        // Validate outcome
        $expectedMessages = array(
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeOne->getAttributeCode()),
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeTwo->getAttributeCode()),
        );
        $this->_createProductWithErrorMessagesCheck($productData, $expectedMessages);
    }

    /**
     * Try to create product using API and check received error messages
     *
     * @param array $productData
     * @param array|string $expectedMessages
     */
    protected function _createProductWithErrorMessagesCheck($productData, $expectedMessages)
    {
        try {
            $this->_tryToCreateProductWithApi($productData);
            $this->fail('SoapFault exception was expected to be raised.');
        } catch (SoapFault $e) {
            $this->_checkErrorMessagesInResponse($e, $expectedMessages);
        }
    }

    /**
     * Create product with API
     *
     * @param array $productData
     * @return int
     */
    protected function _createProductWithApi($productData)
    {
        $productId = (int)$this->_tryToCreateProductWithApi($productData);
        $this->assertGreaterThan(0, $productId, 'Response does not contain valid product ID.');
        return $productId;
    }

    /**
     * Try to create product with API request
     *
     * @param array $productData
     * @return int
     */
    protected function _tryToCreateProductWithApi($productData)
    {
        $dataFormattedForRequest = array(
            'type' => $productData['type_id'],
            'set' => $productData['attribute_set_id'],
            'sku' => $productData['sku'],
            'productData' => $productData
        );
        $response = $this->call('product.create', $dataFormattedForRequest);
        return $response;
    }

    /**
     * Check if expected messages contained in the SoapFault exception
     *
     * @param SoapFault $e
     * @param array|string $expectedMessages
     */
    protected function _checkErrorMessagesInResponse(SoapFault $e, $expectedMessages)
    {
        $expectedMessages = is_array($expectedMessages) ? $expectedMessages : array($expectedMessages);
        $receivedMessages = array();
        // TODO: Change implementation below when multiple error messages in API response will be possible
        $receivedMessages[] = $e->getMessage();
        $this->assertMessagesEqual($expectedMessages, $receivedMessages);
    }
}
