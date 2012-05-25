<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test configurable product resource as admin role.
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_Configurable_AdminTest extends Api2_Catalog_Products_AdminAbstract
{
    /**
     * Test successful configurable product single GET. Check received configurable attributes
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @resourceOperation product::get
     */
    public function testGet()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $this->assertNotEmpty($configurable->getId(), "Configurable product fixture is invalid.");
        $restResponse = $this->callGet($this->_getResourcePath($configurable->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(), "Response status is invalid.");
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $configurable->getData();
        $this->_checkSimpleAttributes($originalData, $responseData);
        $fieldsMap = $fieldsMap = array('frontend_label_use_default' => 'use_default',
            'frontend_label' => 'label', 'position' => 'position');
        $this->_checkConfigurableAttributesInGet($configurable, $responseData, $fieldsMap);
    }

    /**
     * Test successful configurable product single GET. Check received configurable attributes on specified store
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @magentoDataFixture Api2/Catalog/_fixtures/store_on_new_website.php
     * @resourceOperation product::get
     */
    public function testGetOnSpecifiedStore()
    {
        /** @var $customStore Mage_Core_Model_Store */
        $customStore = $this->getFixture('store_on_new_website');
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $this->_updateConfigurableAttributesOnStore($configurable, $customStore);
        $this->assertNotEmpty($configurable->getId(), "Configurable product fixture is invalid.");
        // check values on the custom store
        $this->_testGetOnStore($configurable, $customStore);
        // check values on the default store
        $this->_testGetOnStore($configurable, Mage::app()->getDefaultStoreView());
    }

    /**
     * Make sure that unnecessary fields are not present in the response result for the configurable product.
     * All other behavior is the same as for the simple product
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @resourceOperation product::multiget
     */
    public function testMultiGet()
    {
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        $requestParams = array(
            'filter[0][attribute]' => "entity_id",
            'filter[0][eq][0]' => $configurable->getId(),
        );
        $restResponse = $this->callGet($this->_getResourcePath(), $requestParams);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $resultProducts = $restResponse->getBody();
        $this->assertEquals(1, count($resultProducts), "Invalid products quantity in response.");
        $responseData = reset($resultProducts);
        $this->assertEquals($responseData['entity_id'], $configurable->getId(),
            "Data for the invalid product received.");
        $this->_checkUnnecessaryFields($configurable, $responseData);
    }

    /**
     * Test successful configurable product POST.
     * Scenario:
     * 1. Create EAV attributes and attribute set usable for configurable.
     * 2. Send POST request to create product with type 'configurable' and all valid attributes data.
     * Expected result:
     * Load product and assert it was created correctly.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/attribute_set.php
     * @resourceOperation product::create
     */
    public function testCreate()
    {
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = $this->getFixture('attribute_set_with_configurable');
        /** @var $attributeOne Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        /** @var $attributeTwo Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeTwo = $this->getFixture('eav_configurable_attribute_2');
        $productData['attribute_set_id'] = $attributeSet->getId();
        /** @var $attributeOneSource Mage_Eav_Model_Entity_Attribute_Source_Table */
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

        // Exercise SUT
        $productId = $this->_createProductWithApi($productData);

        /** @var $actual Mage_Catalog_Model_Product */
        $actual = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
        $this->addModelToDelete($actual, true);
        $this->_checkConfigurableAttributesData($actual, $productData['configurable_attributes'], false);
        // Validate outcome
        unset($productData['configurable_attributes']);
        $expected = new Mage_Catalog_Model_Product();
        $expected->setData($productData);
        $this->assertProductsEquals($expected, $actual);
    }

    /**
     * Test configurable product POST pre-validation.
     * Scenario:
     * 1. Send POST request to create product with type 'configurable' and default attribute set which does not contain
     *    any configurable attributes by default.
     * Expected result:
     * Assert that correct error message was returned in the response.
     *
     * @resourceOperation product::create
     */
    public function testCreateInvalidAttributeSet()
    {
        // Prepare fixture
        $productData = $this->_getValidProductPostData();
        /** @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $productData['attribute_set_id'] = $entityType->getDefaultAttributeSetId();

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response,
            "The specified attribute set does not contain attributes which can be used for the configurable product.");
    }

    /**
     * Test configurable product POST pre-validation.
     * Scenario:
     * 1. Create attributes:
     *   'valid_attribute' with scope "Global", input type "Dropdown", and Use To Create Configurable Product "Yes".
     *   'invalid_attribute' with scope "Global", input type "Dropdown".
     * 2. Create an Attribute Set and add attributes from step 1.
     * 3. Try to create Configurable with Attr. Set from step 2.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/attribute_set_with_invalid_attribute.php
     * @resourceOperation product::create
     */
    public function testCreateInvalidAttribute()
    {
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

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response, array(
            sprintf('The attribute with code "%s" cannot be used to create a configurable product.',
                $invalidAttribute->getAttributeCode()),
            'The attribute with code "NOT_EXISTING_ATTRIBUTE" cannot be used to create a configurable product.'
        ));
    }

    /**
     * Test configurable product POST pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" with 2 options values: 1 and 2.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify invalid price and invalid price type in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/attribute_set_with_one_attribute.php
     * @resourceOperation product::create
     */
    public function testCreateInvalidAttributePrice()
    {
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
                    'price' => 5
                )
            )
        ));

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response, array(
            sprintf('The "price" value for the option value "%s" in the "prices" '
                . 'array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[0]['value'], $attribute->getAttributeCode()),
            sprintf('The "price_type" value for the option value "%s" in the '
                . '"prices" array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[0]['value'], $attribute->getAttributeCode()),
            sprintf('The "price_type" value for the option value "%s" in the '
                . '"prices" array for the configurable attribute with code "%s" is invalid.',
                $attributeSourceOptions[1]['value'], $attribute->getAttributeCode())
        ));
    }

    /**
     * Test configurable product POST pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" without any options.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify invalid option_value in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/attribute_set_with_one_attribute.php
     * @resourceOperation product::create
     */
    public function testCreateInvalidAttributeOptionValue()
    {
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
                ),
            )
        ));

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response, array(
            "The \"option_value\" value \"invalid_option_value\" for the configurable attribute"
            ." with code \"{$attribute->getAttributeCode()}\" is invalid."
        ));
    }
    /**
     * Test configurable product POST pre-validation.
     * Scenario:
     * 1. Create attribute test_config with scope "Global", input type "Dropdown"
     *    and Use To Create Configurable Product "Yes" without any options.
     * 2. Create an Attribute Set and add attribute from step 1.
     * 3. Create Configurable with Attr. Set from step 2 and specify empty frontend label for one attribute
     *    and do not specify frontend label for second attribute in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/attribute_set.php
     * @resourceOperation product::create
     */
    public function testCreateInvalidFrontendLabel()
    {
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

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response, array(
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeOne->getAttributeCode()),
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeTwo->getAttributeCode()),
        ));
    }

    /**
     * Test successful configurable product create with multicall.
     * Scenario:
     * 1. Create configurable product.
     * 2. Assign existing simple product via multicall.
     * 3. Create and assign simple product via multicall.
     * Expected result:
     * Assert that created configurable product has two simple products assigned to it.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/multicall.php
     * @resourceOperation product::create
     */
    public function testCreateMulticall()
    {
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
                    'price' => rand(1,100),
                    'price_type' => 'fixed'
                ),
                array(
                    'option_value' => $attributeSourceOptions[1]['value'],
                    'price' => rand(1,25),
                    'price_type' => 'percent'
                )
            )
        ));

        /** @var $simpleProduct Mage_Catalog_Model_Product */
        $simpleProduct = $this->getFixture('simple_product_for_configurable');
        $newSimpleProductData = require dirname(__FILE__) . '/../../_fixtures/Backend/SimpleProductData.php';
        $newSimpleProductData['attribute_set_id'] = $attributeSet->getId();
        $newSimpleProductData[$attribute->getAttributeCode()] = $attributeSourceOptions[1]['value'];
        $productData['configurable_associated_product'] = array(
            array(
                'product_id' => $simpleProduct->getId()
            ),
            $newSimpleProductData
        );

        // Exercise SUT
        $productId = $this->_createProductWithApi($productData);

        // Validate outcome
        /** @var $configurableProduct Mage_Catalog_Model_Product */
        $configurableProduct = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
        $this->addModelToDelete($configurableProduct, true);
        $this->_checkConfigurableAttributesData($configurableProduct, $productData['configurable_attributes']);
        $associatedproducts = array();
        $newSimpleProduct = null;
        /** @var $actualProduct Mage_Catalog_Model_Product */
        foreach ($configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct) as $actualProduct) {
            $associatedproducts[$actualProduct->getId()] = $actualProduct;
            // @TODO: refactor getting created simple product after returning it in multicall will be implemented
            if ($actualProduct->getId() != $simpleProduct->getId()) {
                $newSimpleProduct = clone $actualProduct;
                $this->addModelToDelete($newSimpleProduct, true);
            }
        }

        $this->assertCount(2, $associatedproducts, 'There should be two associated simple products in this test.');
        $this->assertTrue(isset($associatedproducts[$simpleProduct->getId()]),
            'Exisiting simple product was not found in configurable assigned products.');
        $this->assertNotNull($newSimpleProduct,
            'Created simple product was not found in configurable assigned products.');
        $expectedProduct = new Mage_Catalog_Model_Product();
        $expectedProduct->setData($newSimpleProductData);
        $this->assertProductsEquals($expectedProduct, $newSimpleProduct);
    }

    /**
     * Test configurable product create multicall partial success.
     * Scenario:
     * 1. Create configurable product.
     * 2. Assign valid existing simple product via multicall.
     * 3. Try to create and assign invalid simple product via multicall.
     * Expected result:
     * Assert that correct HTTP status was returned and that only one product was assigned to configurable product.
     * Assert that invalid simple product was deleted from system when assigning failed.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/multicall.php
     * @resourceOperation product::create
     */
    public function testCreateMulticallPartialSuccess()
    {
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
                    'price' => rand(1,100),
                    'price_type' => 'fixed'
                ),
                array(
                    'option_value' => $attributeSourceOptions[1]['value'],
                    'price' => rand(1,25),
                    'price_type' => 'percent'
                )
            )
        ));

        /** @var $simpleProduct Mage_Catalog_Model_Product */
        $simpleProduct = $this->getFixture('simple_product_for_configurable');
        // New simple product data is invalid for configurable product because wrong attribute set id specified.
        $newSimpleProductData = require dirname(__FILE__) . '/../../_fixtures/Backend/SimpleProductData.php';
        $productData['configurable_associated_product'] = array(
            array(
                'product_id' => $simpleProduct->getId()
            ),
            $newSimpleProductData
        );

        // Exercise SUT
        $response = $this->_tryToCreateProductWithApi($productData);

        // Validate outcome
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_CREATED, $response->getStatus(),
            'Unexpected HTTP Satus Code while creating configurable product.');
        $productId = $this->_getProductIdFromResponse($response);

        /** @var $configurableProduct Mage_Catalog_Model_Product */
        $configurableProduct = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
        $this->addModelToDelete($configurableProduct, true);
        /** @var $typeInstance Mage_Catalog_Model_Product_Type_Configurable */
        $typeInstance = $configurableProduct->getTypeInstance();
        $assignedProducts = $typeInstance->getUsedProducts($configurableProduct);
        $this->assertCount(1, $assignedProducts, 'There should be only one associated simple product in this test.');
        /** @var $assignedProduct Mage_Catalog_Model_Product */
        $assignedProduct = reset($assignedProducts);
        $this->assertEquals($simpleProduct->getId(), $assignedProduct->getId(), 'Incorrect assigned product.');

        $this->_checkSuccessMessagesInResponse($response, 'Subresource created.');
        $this->_checkErrorMessagesInResponse($response, 'configurable_associated_product: The product to be associated'
            .' must have the same attribute set as the configurable one.', Mage_Api2_Model_Server::HTTP_CREATED);

        $this->assertNull(Mage::getModel('Mage_Catalog_Model_Product')->load($newSimpleProductData['sku'])->getId(),
            'Simple product should be deleted when assiging failed.');
    }

    /**
     * Test successful configurable product PUT.
     * Scenario:
     * 1. Update configurable product attributes label, price and price type.
     * Expected result:
     * Load product and assert it was updated correctly.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @resourceOperation product::update
     */
    public function testUpdate()
    {
        // Prepare fixture
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        $productData = array();
        $productData['configurable_attributes'] = array();
        /** @var $configurableAttribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
        foreach ($configurableType->getConfigurableAttributes($configurable) as $configurableAttribute) {
            $prices = array();
            $productAttribute = $configurableAttribute->getProductAttribute();
            foreach ($productAttribute->getSource()->getAllOptions(false) as $option) {
                foreach ($configurableType->getUsedProducts($configurable) as $associatedProduct) {
                    if (!empty($option['value']) && !isset($prices[$option['value']])
                        && $option['value'] == $associatedProduct->getData($productAttribute->getAttributeCode())) {
                        $prices[$option['value']] = array(
                            'option_value' => $option['value'],
                            'price' => rand(1, 50),
                            'price_type' => rand(0, 1) ? 'fixed' : 'percent'
                        );
                    }
                }
            }
            $productData['configurable_attributes'][] = array(
                'attribute_code' => $configurableAttribute->getProductAttribute()->getAttributeCode(),
                'frontend_label' => $configurableAttribute->getLabel() . ' Updated',
                'frontend_label_use_default' => rand(0, 1),
                'position' => rand(1, 10),
                'prices' => $prices
            );
        }

        // Exercise SUT
        $this->_updateProductWithApi($configurable->getId(), $productData);

        // Validate outcome
        /** @var $productAfterUpdate Mage_Catalog_Model_Product */
        $productAfterUpdate = Mage::getModel('Mage_Catalog_Model_Product')->load($configurable->getId());
        $this->_checkConfigurableAttributesData($productAfterUpdate, $productData['configurable_attributes']);
    }

    /**
     * Test configurable product PUT pre-validation.
     * Scenario:
     * 1. Create configurable product with assigned simple products
     * 3. Try to update created product and specify invalid price, invalid price type, invalid position
     *    and empty frontend_label in request.
     * Expected result:
     * Assert that correct error messages were returned in the response.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_with_assigned_products.php
     * @resourceOperation product::update
     */
    public function testUpdatePreValidation()
    {
        // Prepare fixture
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $attributeOne Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        $attributeOneLastOption = end($attributeOne->getSource()->getAllOptions());
        /** @var $attributeTwo Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeTwo = $this->getFixture('eav_configurable_attribute_2');
        $attributeTwoLastOption = end($attributeTwo->getSource()->getAllOptions());
        $productData['configurable_attributes'] = array(
            array(
                'attribute_code' => $attributeOne->getAttributeCode(),
                'frontend_label' => '',
                'position' => 'invalid',
                'prices' => array(array(
                    'option_value' => $attributeOneLastOption['value'],
                    'price' => 'invalid'
                ))
            ),
            array(
                'attribute_code' => $attributeTwo->getAttributeCode(),
                'frontend_label' => '  ',
                'position' => -1,
                'prices' => array(
                    array(
                        'option_value' => $attributeTwoLastOption['value'],
                        'price_type' => 'invalid'
                    ),
                    array(
                        'option_value' => 'invalid'
                    )
                )
            )
        );

        // Exercise SUT
        $response = $this->_tryToUpdateProductWithApi($configurable->getId(), $productData);

        // Validate outcome
        $this->_checkErrorMessagesInResponse($response, array(
            sprintf('The "price" value for the option value "%s" in the "prices" '
                . 'array for the configurable attribute with code "%s" is invalid.',
                $attributeOneLastOption['value'], $attributeOne->getAttributeCode()),
            sprintf('The "price_type" value for the option value "%s" in the '
                . '"prices" array for the configurable attribute with code "%s" is invalid.',
                $attributeTwoLastOption['value'], $attributeTwo->getAttributeCode()),
            sprintf('The "position" value for the configurable attribute with code "%s" '
                . 'is expected to be a positive integer.', $attributeOne->getAttributeCode()),
            sprintf('The "position" value for the configurable attribute with code "%s" '
                . 'is expected to be a positive integer.', $attributeTwo->getAttributeCode()),
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeOne->getAttributeCode()),
            sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                . 'is required.', $attributeTwo->getAttributeCode()),
            sprintf('The "option_value" value "invalid" for the configurable attribute with '
                . 'code "%s" is invalid.', $attributeTwo->getAttributeCode())
        ));
    }

    /**
     * Test successful configurable product PUT on store view.
     * Scenario:
     * 1. Update configurable product attributes label, price and price type on test store only.
     * Expected result:
     * Load product and assert it was updated correctly on test store and not on default store.
     *
     * @magentoDataFixture Api2/Catalog/Products/Configurable/_fixtures/configurable_on_new_store.php
     * @resourceOperation product::update
     */
    public function testUpdateOnStore()
    {
        // Prepare fixture
        /** @var $configurable Mage_Catalog_Model_Product */
        $configurable = $this->getFixture('product_configurable');
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance(true);
        // Store configurable product data from default store for future comparison
        $productDataOnDefaultStore = array();
        $productDataOnDefaultStore['configurable_attributes'] = array();
        foreach($configurableType->getConfigurableAttributesAsArray($configurable) as $attribute){
            $prices = array();
            foreach ($attribute['values'] as $value) {
                $prices[] = array(
                    'option_value' => $value['value_index'],
                    'price' => $value['pricing_value'],
                    'price_type' => $value['is_percent'] ? 'percent' : 'fixed'
                );
            }
            $productDataOnDefaultStore[] = array(
                'attribute_code' => $attribute['attribute_code'],
                'frontend_label' => $attribute['label'],
                'frontend_label_use_default' => $attribute['use_default'],
                'position' => $attribute['position']
            );
        }
        // Prepare data for update on test store
        /** @var $testStore Mage_Core_Model_Store */
        $testStore = $this->getFixture('store_on_new_website');
        /** @var $attributeOne Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeOne = $this->getFixture('eav_configurable_attribute_1');
        $attributeOneLastOption = end($attributeOne->getSource()->getAllOptions());
        $productDataOnTestStore = array();
        $productDataOnTestStore['configurable_attributes'] = array(
            array(
                'attribute_code' => $attributeOne->getAttributeCode(),
                'frontend_label' => $attributeOne->getFrontendLabel() . 'Updated On Test Store',
                'frontend_label_use_default' => rand(0, 1),
                'position' => rand(1, 10),
                'prices' => array(array(
                    'option_value' => $attributeOneLastOption['value'],
                    'price' => rand(1, 100),
                    'price_type' => rand(0, 1) ? 'fixed' : 'percent'
                ))
            )
        );

        // Exercise SUT
        $this->_updateProductWithApi($configurable->getId(), $productDataOnTestStore, $testStore->getId());

        // Validate outcome
        /** @var $productOnTestStore Mage_Catalog_Model_Product */
        $productOnTestStore = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId($testStore->getId())
            ->load($configurable->getId());
        $this->_checkConfigurableAttributesData($productOnTestStore,
            $productDataOnTestStore['configurable_attributes']);

        /** @var $productOnDefaultStore Mage_Catalog_Model_Product */
        $productOnDefaultStore = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(0)->load($configurable->getId());
        $this->_checkConfigurableAttributesData($productOnDefaultStore,
            $productDataOnDefaultStore['configurable_attributes']);
    }

    /**
     * Check if the configurable attributes' data was saved correctly during create
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $expectedConfigurableData
     * @param bool $validatePrices
     */
    protected function _checkConfigurableAttributesData($configurable, $expectedConfigurableData,
        $validatePrices = true)
    {
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance(true);
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
     * Perform get test on the specified store
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param Mage_Core_Model_Store $store
     */
    protected function _testGetOnStore($configurable, $store)
    {
        $restResponse = $this->callGet($this->_getResourcePath($configurable->getId(), $store->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(), "Response status is invalid.");
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $configurable = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId($store->getId())->load($configurable->getId());
        $fieldsMap = $fieldsMap = array('frontend_label_use_default' => 'use_default',
            'frontend_label' => 'label', 'position' => 'position');
        $this->_checkConfigurableAttributesInGet($configurable, $responseData, $fieldsMap);
    }

    /**
     * Check if configurable option prices in the response are correct
     *
     * @param array $prices
     * @param array $pricesInResponse
     * @param Mage_Catalog_Model_Product|null $configurable
     * @param string $attributeCode
     */
    protected function _checkOptionPrices($prices, $pricesInResponse, $configurable, $attributeCode)
    {
        foreach ($prices as $price) {
            $isPriceValueFoundInResponse = false;
            $optionValue = $price['value_index'];
            foreach ($pricesInResponse as $priceInResponse) {
                if ($priceInResponse['option_value'] == $optionValue) {
                    $fieldsMap = array('option_label' => 'label', 'price' => 'pricing_value');
                    foreach ($fieldsMap as $fieldInResponse => $field) {
                        $this->assertArrayHasKey($fieldInResponse, $priceInResponse,
                            "The '$fieldInResponse' field must be defined for the '$optionValue' option "
                                . "related to the '$attributeCode' attribute.");
                        $this->assertEquals($price[$field], $priceInResponse[$fieldInResponse],
                            "The '$fieldInResponse' field has invalid value for the '$optionValue' option "
                                . "related to the '$attributeCode' attribute.");
                    }
                    $this->assertArrayHasKey('price_type', $priceInResponse,
                        "The 'price_type' field must be defined for the '$optionValue' option "
                            . "related to the '$attributeCode' attribute.");
                    $expectedPriceType = $price['is_percent'] ? 'percent' : 'fixed';
                    $this->assertEquals($expectedPriceType, $priceInResponse['price_type'],
                        "The 'price_type' field has invalid value for the '$optionValue' option "
                            . "related to the '$attributeCode' attribute.");
                    $isPriceValueFoundInResponse = true;
                    break;
                }
            }
            $this->assertTrue($isPriceValueFoundInResponse, "The information about '$optionValue' option "
                . "for the configurable attribute with code "
                . "'$attributeCode' not found in the 'prices' array.");
        }
    }

    /**
     * Prepare configurable attributes for the GET test on specified store
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param Mage_Core_Model_Store $store
     */
    protected function _updateConfigurableAttributesOnStore($configurable, $store)
    {
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        $configurableAttributes = $configurableType->getConfigurableAttributesAsArray($configurable);
        foreach ($configurableAttributes as &$configurableAttribute) {
            $configurableAttribute['label'] .= " " . $store->getCode();
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute')
                ->load($configurableAttribute['attribute_id']);
            /** @var $source Mage_Eav_Model_Entity_Attribute_Source_Table */
            $source = $attribute->getSource();
            $options = array();
            foreach ($source->getAllOptions(false) as $option) {
                $options[$option['value']] = array(
                    0 => $option['label'],
                    $store->getId() => $option['label'] . " " . $store->getCode()
                );
            }
            $attribute->setFrontendLabel(array(0 => $attribute->getFrontendLabel(),
                $store->getId() => $attribute->getFrontendLabel() . " " . $store->getCode()));
            $attribute->setOption(array('value' => $options));
            $attribute->save();
            foreach ($configurableAttribute['values'] as &$value) {
                $value['label'] .= " " . $store->getCode();
                $value['pricing_value'] = rand(1, 1000);
                $value['is_percent'] = rand(0, 1);
            }
        }
        $configurable->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId(), $store->getWebsiteId()))
            ->setStoreId($store->getId())
            ->setConfigurableAttributesData($configurableAttributes)
            ->setCanSaveConfigurableAttributes(true)
            ->save();
    }

    /**
     * Get valid data for configurable product POST
     *
     * @return array
     */
    protected function _getValidProductPostData()
    {
        return require dirname(__FILE__) . '/_fixtures/DataProvider/ProductConfigurableAllFields.php';
    }
}
