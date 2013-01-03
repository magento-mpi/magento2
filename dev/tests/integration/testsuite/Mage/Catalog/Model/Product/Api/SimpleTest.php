<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test Product CRUD operations
 *
 * @method Mage_Catalog_Model_Product_Api_Helper_Simple _getHelper()
 */
class Mage_Catalog_Model_Product_Api_SimpleTest extends Mage_Catalog_Model_Product_Api_TestCaseAbstract
{
    /**
     * Default helper for current test suite
     *
     * @var string
     */
    protected $_defaultHelper = 'Mage_Catalog_Model_Product_Api_Helper_Simple';

    /**
     * Test product resource post
     */
    public function testCreateSimpleRequiredFieldsOnly()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_data');
        $productId = $this->_createProductWithApi($productData);

        $actualProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $actualProduct->load($productId);
        $this->assertNotNull($actualProduct->getId());
        $expectedProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $expectedProduct->setData($productData);

        $this->assertProductEquals($expectedProduct, $actualProduct);
    }

    /**
     * Test product resource post with all fields
     *
     * @param array $productData
     * @dataProvider dataProviderTestCreateSimpleAllFieldsValid
     */
    public function testCreateSimpleAllFieldsValid($productData)
    {
        $productId = $this->_createProductWithApi($productData);

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $skipAttributes = array(
            'news_from_date',
            'news_to_date',
            'custom_design_from',
            'custom_design_to',
            'msrp_enabled',
            'msrp_display_actual_price_type',
            'msrp',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'page_layout',
            'gift_wrapping_available',
            'gift_wrapping_price'
        );
        $skipStockItemAttributes = array('min_qty');

        $this->_getHelper()->checkSimpleAttributesData(
            $product,
            $productData,
            $skipAttributes,
            $skipStockItemAttributes
        );
    }

    /**
     * Data provider for testCreateSimpleAllFieldsValid
     *
     * @return array
     */
    public function dataProviderTestCreateSimpleAllFieldsValid()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_all_fields_data');
        // Fix for tests, because in current soap version this field has "int" type in WSDL
        // @TODO: fix WSDL in new soap version when implemented
        $productData['stock_data']['notify_stock_qty'] = 2;
        $productDataSpecialChars = $this->_getHelper()
            ->loadSimpleProductFixtureData('simple_product_special_chars_data');

        return array(
            array($productDataSpecialChars),
            array($productData),
        );
    }

    /**
     * Test product resource post with all invalid fields
     * Negative test.
     */
    public function testCreateSimpleAllFieldsInvalid()
    {
        $this->markTestSkipped("This test fails due to absence of proper validation in the functionality itself.");
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_all_fields_invalid_data');
        // Tier price validation implemented differently in soap
        unset($productData['tier_price']);
        $expectedErrors = array(
            'SKU length should be 64 characters maximum.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.',
            'Please enter a number 0 or greater in the "min_qty" field in the "stock_data" set.',
            'Please use numbers only in the "min_sale_qty" field in the "stock_data" set. '
                . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "max_sale_qty" field in the "stock_data" set. '
                . 'Please avoid spaces or other non numeric characters.',
            'Invalid "backorders" value in the "stock_data" set.',
            'Invalid "is_in_stock" value in the "stock_data" set.',
        );
        $invalidValueAttributes = array(
            'status',
            'visibility',
            'tax_class_id',
            'custom_design',
            'gift_message_available'
        );
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value "%s" for attribute "%s".', $productData[$attribute], $attribute);
        }
        $dateAttributes = array('special_from_date', 'special_to_date');
        foreach ($dateAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid date in the "%s" field.', $attribute);
        }
        $positiveNumberAttributes = array('weight', 'price', 'special_price');
        foreach ($positiveNumberAttributes as $attribute) {
            $expectedErrors[] = sprintf('Please enter a number 0 or greater in the "%s" field.', $attribute);
        }

        $this->_createProductWithErrorMessagesCheck($productData, $expectedErrors);
    }

    /**
     * Test product create resource with invalid qty uses decimals value
     * Negative test.
     */
    public function testCreateInvalidQtyUsesDecimals()
    {
        $this->markTestSkipped("This test fails due to absence of proper validation in the functionality itself.");
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_invalid_qty_uses_decimals');

        $this->_createProductWithErrorMessagesCheck(
            $productData,
            'Invalid "is_qty_decimal" value in the "stock_data" set.'
        );
    }

    /**
     * Test product create resource with invalid weight value
     * Negative test.
     */
    public function testCreateWeightOutOfRange()
    {
        $this->markTestSkipped("This test fails due to absence of proper validation in the functionality itself.");
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_weight_out_of_range');

        $this->_createProductWithErrorMessagesCheck(
            $productData,
            'The "weight" value is not within the specified range.'
        );
    }

    /**
     * Test product create resource with not unique sku value
     * Negative test.
     *
     * @magentoDataFixture Api/Mage/SalesOrder/_files/product_simple.php
     */
    public function testCreateNotUniqueSku()
    {
        $this->markTestSkipped("This test fails due to absence of proper validation in the functionality itself.");
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product_simple');
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_data');
        $productData['sku'] = $product->getSku();

        $this->_createProductWithErrorMessagesCheck(
            $productData,
            'Invalid attribute "sku": The value of attribute "SKU" must be unique'
        );
    }

    /**
     * Test product create resource with empty required fields
     * Negative test.
     *
     * @param array $productData
     * @dataProvider dataProviderTestCreateEmptyRequiredFields
     */
    public function testCreateEmptyRequiredFields($productData)
    {
        $this->markTestSkipped("This test fails due to absence of proper validation in the functionality itself.");
        $expectedErrors = array(
            'Please enter a valid number in the "qty" field in the "stock_data" set.'
        );
        $errorFields = array_diff_key(
            $productData,
            array_flip(
                array('type_id', 'attribute_set_id', 'sku', 'stock_data')
            )
        );
        foreach ($errorFields as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->_createProductWithErrorMessagesCheck($productData, $expectedErrors);
    }

    /**
     * Data provider for testCreateEmptyRequiredFields
     *
     * @return array
     */
    public function dataProviderTestCreateEmptyRequiredFields()
    {
        $productDataEmpty = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_empty_required');
        $productDataStringsEmptySpaces = $this->_getHelper()
            ->loadSimpleProductFixtureData('simple_product_empty_spaces_required');

        return array(
            array($productDataEmpty),
            array($productDataStringsEmptySpaces),
        );
    }

    /**
     * Test product resource post using config values in inventory
     */
    public function testCreateInventoryUseConfigValues()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_inventory_use_config');
        $productId = $this->_createProductWithApi($productData);

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNotNull($product->getId());

        $this->_getHelper()->checkStockItemDataUseDefault($product);
    }

    /**
     * Test product resource post using config values in inventory manage stock field
     *
     * @magentoConfigFixture current_store cataloginventory/item_options/manage_stock 0
     */
    public function testCreateInventoryManageStockUseConfig()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_manage_stock_use_config');

        $productId = $this->_createProductWithApi($productData);
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNotNull($product->getId());

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $this->assertEquals(0, $stockItem->getManageStock());
    }

    /**
     * Test for set special price for product
     */
    public function testSetSpecialPrice()
    {
        $productData = require dirname(__FILE__) . '/_files/ProductData.php';
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $specialPrice = 1.99;
        $specialFrom = '2011-12-22 00:00:00';
        $specialTo = '2011-12-25 00:00:00';

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductSetSpecialPrice',
            array(
                'productId' => $product->getSku(),
                'specialPrice' => $specialPrice,
                'fromDate' => $specialFrom,
                'toDate' => $specialTo,
                'store' => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
            )
        );

        $this->assertEquals(true, $result, 'Response is not true casted value');

        // reload product to reflect changes done by API request
        $product->load($product->getId());

        $this->assertEquals($specialPrice, $product->getSpecialPrice(), 'Special price not changed');
        $this->assertEquals($specialFrom, $product->getSpecialFromDate(), 'Special price from not changed');
        $this->assertEquals($specialTo, $product->getSpecialToDate(), 'Special price to not changed');
    }

    /**
     * Test get product info by numeric SKU
     */
    public function testProductInfoByNumericSku()
    {
        $data = require dirname(__FILE__) . '/_files/ProductData.php';

        //generate numeric sku
        $data['create_with_attributes_soapv2']->sku = rand(1000000, 99999999);

        $id = Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $data['create']);

        $this->assertEquals(
            $id,
            (int)$id,
            'Result of a create method is not an integer.'
        );

        //test new product exists in DB
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($id);
        $this->assertNotNull($product->getId(), 'Tested product not found.');

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductInfo',
            array(
                'productId' => $data['create']['sku'],
                'store' => 0, //default 0
                'attributes' => '',
                'identifierType' => 'sku',
            )
        );

        $this->assertInternalType('array', $result, 'Response is not an array');
        $this->assertArrayHasKey('product_id', $result, 'Response array does not have "product_id" key');
        $this->assertEquals($id, $result['product_id'], 'Product cannot be load by SKU which is numeric');
    }

    /**
     * Test product CRUD
     */
    public function testProductCrud()
    {
        $data = require dirname(__FILE__) . '/_files/ProductData.php';

        // create product for test
        $productId = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCreate',
            $data['create_with_attributes_soapv2']
        );

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        //test new product exists in DB
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNotNull($product->getId());

        //update product
        $data['create_with_attributes_soapv2'] = array('productId' => $productId) + $data['update'];

        $isOk = Magento_Test_Helper_Api::call($this, 'catalogProductUpdate', $data['create_with_attributes_soapv2']);

        //test call response is true
        $this->assertTrue($isOk, 'Call returned false');

        //test product exists in DB after update and product data changed
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->assertEquals($data['update']['productData']->name, $product->getName());

        //delete product
        $isOk = Magento_Test_Helper_Api::call($this, 'catalogProductDelete', array('productId' => $productId));

        //test call response is true
        $this->assertTrue((bool)$isOk, 'Call returned false'); //in SOAP v2 it's integer:1

        //test product not exists in DB after delete
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);
        $this->assertNull($product->getId());
    }

    /**
     * Test product CRUD with custom options
     *
     * @magentoDataFixture Mage/Catalog/Model/Product/Api/_files/ProductWithOptionCrud.php
     */
    public function testProductWithOptionsCrud()
    {
        $this->markTestSkipped("TODO: Fix test");
        $optionValueApi = Mage::registry('optionValueApi');
        $optionValueInstaller = Mage::registry('optionValueInstaller');
        $data = require dirname(__FILE__) . '/_files/ProductData.php';

        $singleData = & $data['create_with_attributes_soapv2']['productData']->additional_attributes->single_data;
        $singleData[1]->value = $optionValueApi;
        $singleData[3]->value = $optionValueInstaller;
        $attributes = $data['create_with_attributes_soapv2']['productData']->additional_attributes;

        // create product for test
        $productId = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductCreate',
            $data['create_with_attributes_soapv2']
        );

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        //test new product attributes
        $this->assertEquals($attributes->single_data[0]->value, $product->getData('a_text_api'));
        $this->assertEquals($attributes->single_data[1]->value, $product->getData('a_select_api'));
        $this->assertEquals($attributes->single_data[2]->value, $product->getData('a_text_ins'));
        $this->assertEquals($attributes->single_data[3]->value, $product->getData('a_select_ins'));

    }

    /**
     * Test create product with invalid attribute set
     */
    public function testProductCreateWithInvalidAttributeSet()
    {
        $productData = require dirname(__FILE__) . '/_files/ProductData.php';
        $productData = $productData['create_full']['soap'];
        $productData['set'] = 'invalid';

        try {
            Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $productData);
        } catch (Exception $e) {
            $this->assertEquals('Product attribute set does not exist.', $e->getMessage(), 'Invalid exception message');
        }

        // find not product (category) attribute set identifier to try other error message
        /** @var $entity Mage_Eav_Model_Entity_Type */
        $entity = Mage::getModel('Mage_Eav_Model_Entity_Type');
        $entityTypeId = $entity->loadByCode('catalog_category')->getId();

        /** @var $attrSet Mage_Eav_Model_Entity_Attribute_Set */
        $attrSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');

        /** @var $attrSetCollection Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
        $attrSetCollection = $attrSet->getCollection();
        $categoryAtrrSets = $attrSetCollection->setEntityTypeFilter($entityTypeId)->toOptionHash();
        $categoryAttrSetId = key($categoryAtrrSets);

        $productData['set'] = $categoryAttrSetId;

        try {
            Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $productData);
        } catch (Exception $e) {
            $this->assertEquals(
                'Product attribute set does not belong to catalog product entity type.',
                $e->getMessage(),
                'Invalid exception message'
            );
        }
    }

    /**
     * Test product attributes update in custom store view
     *
     * @magentoDataFixture Api/_files/Core/Store/store_on_new_website.php
     */
    public function testProductUpdateCustomStore()
    {
        /** @var Mage_Core_Model_Store $store */
        $store = Mage::registry('store_on_new_website');

        $data = require dirname(__FILE__) . '/_files/ProductData.php';
        // create product for test
        $productId = Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $data['create_full']['soap']);
        $this->assertGreaterThan(0, $productId, 'Product was not created');

        // update product on test store
        $data['update_custom_store'] = array('productId' => $productId) + $data['update_custom_store'];
        $data['update_custom_store']['store'] = $store->getCode();
        $isOk = Magento_Test_Helper_Api::call($this, 'catalogProductUpdate', $data['update_custom_store']);
        $this->assertTrue($isOk, 'Can not update product on test store');

        // Load product in test store
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->setStoreId($store->getId())->load($productId);
        $this->assertNotNull($product->getId());
        $this->assertEquals(
            $data['update_custom_store']['productData']->name,
            $product->getName(),
            'Product name was not updated'
        );

        // update product attribute in default store
        $data['update_default_store'] = array('productId' => $productId) + $data['update_default_store'];
        $isOk = Magento_Test_Helper_Api::call($this, 'catalogProductUpdate', $data['update_default_store']);
        $this->assertTrue($isOk, 'Can not update product on default store');

        // Load product in default store
        $productDefault = Mage::getModel('Mage_Catalog_Model_Product');
        $productDefault->load($productId);
        $this->assertEquals(
            $data['update_default_store']['productData']->description,
            $productDefault->getDescription(),
            'Description attribute was not updated for default store'
        );
        $this->assertEquals(
            $data['create_full']['soap']['productData']->name,
            $productDefault->getName(),
            'Product name attribute should not have been changed'
        );

        // Load product in test store
        $productTestStore = Mage::getModel('Mage_Catalog_Model_Product');
        $productTestStore->setStoreId($store->getId())->load($productId);
        $this->assertEquals(
            $data['update_default_store']['productData']->description,
            $productTestStore->getDescription(),
            'Description attribute was not updated for test store'
        );
        $this->assertEquals(
            $data['update_custom_store']['productData']->name,
            $productTestStore->getName(),
            'Product name attribute should not have been changed for test store'
        );
    }

    /**
     * Test create product to test default values for media attributes
     */
    public function testProductCreateForTestMediaAttributesDefaultValue()
    {
        $productData = require dirname(__FILE__) . '/_files/ProductData.php';
        $productData = $productData['create'];

        // create product for test
        $productId = Magento_Test_Helper_Api::call($this, 'catalogProductCreate', $productData);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load($productId);

        $found = false;
        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $this->assertEquals(
                $product->getData($mediaAttrCode),
                'no_selection',
                'Attribute "' . $mediaAttrCode . '" has no default value'
            );
            $found = true;
        }
        $this->assertTrue($found, 'Media attrributes not found');
    }
}
