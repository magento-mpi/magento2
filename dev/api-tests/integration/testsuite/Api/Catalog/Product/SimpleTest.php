<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Product CRUD operations
 *
 * @method Helper_Catalog_Product_Simple _getHelper()
 */
class Api_Catalog_Product_SimpleTest extends Api_Catalog_ProductAbstract
{

    /**
     * Test store model
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Default helper for current test suite
     *
     * @var string
     */
    protected $_defaultHelper = 'Helper_Catalog_Product_Simple';

    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        $product = new Mage_Catalog_Model_Product();
        $product->load($this->getFixture('productId'));
        $this->callModelDelete($product, true);
        $this->callModelDelete($this->_store, true);

        parent::tearDown();
    }

    /**
     * Test product resource post
     *
     * @resourceOperation product::create
     */
    public function testCreateSimpleRequiredFieldsOnly()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_data');
        $productId = $this->_createProductWithApi($productData);

        $actualProduct = new Mage_Catalog_Model_Product();
        $actualProduct->load($productId);
        $this->assertNotNull($actualProduct->getId());
        $this->addModelToDelete($actualProduct, true);
        $expectedProduct = new Mage_Catalog_Model_Product();
        $expectedProduct->setData($productData);

        $this->assertProductEquals($expectedProduct, $actualProduct);
    }

    /**
     * Test product resource post with all fields
     *
     * @param array $productData
     * @dataProvider dataProviderTestCreateSimpleAllFieldsValid
     * @resourceOperation product::create
     */
    public function testCreateSimpleAllFieldsValid($productData)
    {
        $productId = $this->_createProductWithApi($productData);

        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->addModelToDelete($product, true);

        $this->_getHelper()->checkSimpleAttributesData($product, $productData);
    }

    /**
     * Data provider for testCreateSimpleAllFieldsValid
     *
     * @dataSetNumber 2
     * @return array
     */
    public function dataProviderTestCreateSimpleAllFieldsValid()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_all_fields_data');
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
     *
     * @resourceOperation product::create
     */
    public function testCreateSimpleAllFieldsInvalid()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_all_fields_invalid_data');

        $expectedErrors = array(
            'SKU length should be 64 characters maximum.',
            'Invalid "cust_group" value in the "group_price:0" set',
            'Please enter a number 0 or greater in the "price" field in the "group_price:1" set.',
            'Invalid "website_id" value in the "group_price:2" set.',
            'Invalid "website_id" value in the "group_price:3" set.',
            'The "cust_group" value in the "group_price:3" set is a required field.',
            'The "website_id" value in the "group_price:4" set is a required field.',
            'Invalid "website_id" value in the "group_price:5" set.',
            'The "price" value in the "group_price:5" set is a required field.',
            'Invalid "cust_group" value in the "tier_price:0" set',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:1" set.',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:2" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:3" set.',
            'Invalid "website_id" value in the "tier_price:4" set.',
            'Invalid "website_id" value in the "tier_price:5" set.',
            'The "price_qty" value in the "tier_price:7" set is a required field.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:7" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:8" set.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.',
            'Please enter a valid number in the "notify_stock_qty" field in the "stock_data" set.',
            'Please enter a number 0 or greater in the "min_qty" field in the "stock_data" set.',
            'Invalid "is_decimal_divided" value in the "stock_data" set.',
            'Please use numbers only in the "min_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "max_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "qty_increments" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Invalid "backorders" value in the "stock_data" set.',
            'Invalid "is_in_stock" value in the "stock_data" set.',
            'Please enter a number 0 or greater in the "gift_wrapping_price" field.',
            'Invalid "cust_group" value in the "group_price:4" set',
            'Invalid "cust_group" value in the "tier_price:6" set',
        );
        $invalidValueAttributes = array('status', 'visibility', 'msrp_enabled', 'msrp_display_actual_price_type',
            'enable_googlecheckout', 'tax_class_id', 'custom_design', 'page_layout', 'gift_message_available',
            'gift_wrapping_available');
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value "%s" for attribute "%s".', $productData[$attribute], $attribute);
        }
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid date in the "%s" field.', $attribute);
        }
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        foreach ($positiveNumberAttributes as $attribute) {
            $expectedErrors[] = sprintf('Please enter a number 0 or greater in the "%s" field.', $attribute);
        }

        $this->_createProductWithErrorMessagesCheck($productData, $expectedErrors);
    }

    /**
     * Test product create resource with invalid qty uses decimals value
     * Negative test.
     *
     * @resourceOperation product::create
     */
    public function testCreateInvalidQtyUsesDecimals()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_invalid_qty_uses_decimals');

        $this->_createProductWithErrorMessagesCheck($productData,
            'Invalid "is_qty_decimal" value in the "stock_data" set.');
    }

    /**
     * Test product create resource with invalid manage stock value
     * Negative test.
     *
     * @resourceOperation product::create
     */
    public function testCreateInvalidManageStock()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_invalid_manage_stock');

        $this->_createProductWithErrorMessagesCheck($productData,
            'Invalid "manage_stock" value in the "stock_data" set.');
    }

    /**
     * Test product create resource with invalid weight value
     * Negative test.
     *
     * @resourceOperation product::create
     */
    public function testCreateWeightOutOfRange()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_weight_out_of_range');

        $this->_createProductWithErrorMessagesCheck($productData,
            'The "weight" value is not within the specified range.');
    }

    /**
     * Test product create resource with not unique sku value
     * Negative test.
     *
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/product_simple.php
     * @resourceOperation product::create
     */
    public function testCreateNotUniqueSku()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_data');
        $productData['sku'] = $product->getSku();

        $this->_createProductWithErrorMessagesCheck($productData,
            'Invalid attribute "sku": The value of attribute "SKU" must be unique');
    }

    /**
     * Test product create resource with empty required fields
     * Negative test.
     *
     * @param array $productData
     * @resourceOperation product::create
     * @dataProvider dataProviderTestCreateEmptyRequiredFields
     */
    public function testCreateEmptyRequiredFields($productData)
    {
        $expectedErrors = array(
            'Please enter a valid number in the "qty" field in the "stock_data" set.'
        );
        $errorFields = array_diff_key($productData, array_flip(
            array('type_id', 'attribute_set_id', 'sku', 'stock_data')));
        foreach ($errorFields as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->_createProductWithErrorMessagesCheck($productData, $expectedErrors);
    }

    /**
     * Data provider for testCreateEmptyRequiredFields
     *
     * @dataSetNumber 2
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
     *
     * @resourceOperation product::create
     */
    public function testCreateInventoryUseConfigValues()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_inventory_use_config');
        $productId = $this->_createProductWithApi($productData);

        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->addModelToDelete($product, true);

        $this->_getHelper()->checkStockItemDataUseDefault($product);
    }

    /**
     * Test product resource post using config values in inventory manage stock field
     *
     * @resourceOperation product::create
     */
    public function testCreateInventoryManageStockUseConfig()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_manage_stock_use_config');

        $this->_updateAppConfig('cataloginventory/item_options/manage_stock', 0, true, true);

        $productId = $this->_createProductWithApi($productData);
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->addModelToDelete($product, true);

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $this->assertEquals(0, $stockItem->getManageStock());
    }

    /**
     * Test product resource post when manage_stock set to no and inventory data is sent in request
     *
     * @resourceOperation product::create
     */
    public function testCreateInventoryManageStockNo()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_manage_stock_no');

        $productId = $this->_createProductWithApi($productData);
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->addModelToDelete($product, true);

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $this->assertEquals(0, $stockItem->getManageStock());
        $this->assertEquals(0, $stockItem->getQty());
    }

    /**
     * Test product resource post using config values in gift options
     *
     * @resourceOperation product::create
     */
    public function testCreateGiftOptionsUseConfigValues()
    {
        $productData = $this->_getHelper()->loadSimpleProductFixtureData('simple_product_gift_options_use_config');

        $productId = $this->_createProductWithApi($productData);
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->addModelToDelete($product, true);
    }

    /**
     * Test for set special price for product
     */
    public function testSetSpecialPrice()
    {
        $productData  = require dirname(__FILE__) . '/_fixture/ProductData.php';
        $product      = new Mage_Catalog_Model_Product;
        $specialPrice = 1.99;
        $specialFrom  = '2011-12-22 00:00:00';
        $specialTo    = '2011-12-25 00:00:00';

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $result = $this->getWebService()->call(
            'catalog_product.setSpecialPrice',
            array(
                'productId'    => $product->getSku(),
                'specialPrice' => $specialPrice,
                'fromDate'     => $specialFrom,
                'toDate'       => $specialTo,
                'store'        => Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID
            )
        );

        $this->assertEquals(true, $result, 'Response is not true casted value');

        // reload product to reflect changes done by API request
        $product->load($product->getId());

        $this->assertEquals($specialPrice, $product->getSpecialPrice(), 'Special price not changed');
        $this->assertEquals($specialFrom, $product->getSpecialFromDate(), 'Special price from not changed');
        $this->assertEquals($specialTo, $product->getSpecialToDate(), 'Special price to not changed');

        $this->setFixture('productId', $product->getId());
    }

    /**
     * Test get product info by numeric SKU
     *
     * @return void
     */
    public function testProductInfoByNumericSku()
    {
        $data = require dirname(__FILE__) . '/_fixture/ProductData.php';

        //generate numeric sku
        $data['create']['sku'] = rand(1000000, 99999999);

        $id = $this->call('catalog_product.create', $data['create']);

        $this->assertEquals($id, (int) $id,
                'Result of a create method is not an integer.');

        //test new product exists in DB
        $product = new Mage_Catalog_Model_Product();
        $product->load($id);
        $this->setFixture('productId', $product->getId());
        $this->assertNotNull($product->getId(), 'Tested product not found.');

        $result = $this->call('catalog_product.info',
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
     *
     * @return void
     */
    public function testProductCrud()
    {
        $data = require dirname(__FILE__) . '/_fixture/ProductData.php';

        // create product for test
        $productId = $this->call('catalog_product.create', $data['create']);
        $this->setFixture('productId', $productId);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        //test new product exists in DB
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());

        //update product
        $data['update'] = array('productId' => $productId) + $data['update'];

        $isOk = $this->call('catalog_product.update', $data['update']);

        //test call response is true
        $this->assertTrue($isOk, 'Call returned false');

        //test product exists in DB after update and product data changed
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->assertEquals($data['update']['productData']['name'], $product->getName());

        //delete product
        $isOk = $this->call('catalog_product.delete', array('productId' => $productId));

        //test call response is true
        $this->assertTrue((bool)$isOk, 'Call returned false');  //in SOAP v2 it's integer:1

        //test product not exists in DB after delete
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNull($product->getId());
    }

    /**
     * Test product CRUD with custom options
     *
     * @return void
     */
    public function testProductWithOptionsCrud()
    {
        list($optionValueApi, $optionValueInstaller) = $this->_addAttributes();
        $data = require dirname(__FILE__) . '/_fixture/ProductData.php';

        try {
            switch (TESTS_WEBSERVICE_TYPE) {
                case self::TYPE_SOAPV1:
                case self::TYPE_XMLRPC:
                    $this->_testSoapV1($optionValueApi, $optionValueInstaller, $data);
                    break;

                case self::TYPE_SOAPV2:
                    $this->_testSoapV2($optionValueApi, $optionValueInstaller, $data);
                    break;
            }
        } catch (Exception $e) {
            //give delete attributes
        }

        $this->_removeAttributes();

        if (isset($e)) {
            //throw exception if it was catch
            throw $e;
        }

    }

    /**
     * Test for SOAPV1 and XMLRPC
     *
     * Help CRUD method
     *
     * @param int $optionValueApi
     * @param int $optionValueInstaller
     * @param array $data
     * @return void
     */
    protected function _testSoapV1($optionValueApi, $optionValueInstaller, $data)
    {
        $attributes = &$data['create_with_attributes_soap']['productData']['additional_attributes'];
        $attributes['single_data']['a_select_api'] = $optionValueApi;
        $attributes['single_data']['a_select_ins'] = $optionValueInstaller;

        // create product for test
        $productId = $this->call('catalog_product.create', $data['create_with_attributes_soap']);
        $this->setFixture('productId', $productId);

        $product = new Mage_Catalog_Model_Product;
        $product->load($productId);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        //test new product attributes
        $this->assertEquals($attributes['single_data']['a_text_api'], $product->getData('a_text_api'));
        $this->assertEquals($attributes['single_data']['a_select_api'], $product->getData('a_select_api'));
        $this->assertEquals($attributes['single_data']['a_text_ins'], $product->getData('a_text_ins'));
        $this->assertEquals($attributes['single_data']['a_select_ins'], $product->getData('a_select_ins'));
    }

    /**
     * Test for SOAPV2
     *
     * Help CRUD method
     *
     * @param int $optionValueApi
     * @param int $optionValueInstaller
     * @param array $data
     * @return void
     */
    protected function _testSoapV2($optionValueApi, $optionValueInstaller, $data)
    {
        $attributes = &$data['create_with_attributes_soapv2']['productData']['additional_attributes'];
        $attributes['single_data'][1]['value'] = $optionValueApi;
        $attributes['single_data'][3]['value'] = $optionValueInstaller;

        // create product for test
        $productId = $this->call('catalog_product.create', $data['create_with_attributes_soapv2']);
        $this->setFixture('productId', $productId);

        $product = new Mage_Catalog_Model_Product;
        $product->load($productId);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        //test new product attributes
        $this->assertEquals($attributes['single_data'][0]['value'], $product->getData('a_text_api'));
        $this->assertEquals($attributes['single_data'][1]['value'], $product->getData('a_select_api'));
        $this->assertEquals($attributes['single_data'][2]['value'], $product->getData('a_text_ins'));
        $this->assertEquals($attributes['single_data'][3]['value'], $product->getData('a_select_ins'));
    }

    /**
     * Add attributes for tests
     *
     * Help CRUD method
     *
     * @return array
     */
    protected function _addAttributes()
    {
        $this->setFixture('attributes', true);

        $data = require dirname(__FILE__) . '/_fixture/ProductAttributeData.php';


        // add product attributes via installer
        $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
        $installer->addAttribute('catalog_product', $data['create_text_installer']['code'],
            $data['create_text_installer']['attributeData']);
        $installer->addAttribute('catalog_product', $data['create_select_installer']['code'],
            $data['create_select_installer']['attributeData']);

        //add attributes to default attribute set via installer
        $installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_text_installer']['code']);
        $installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_select_installer']['code']);

        $attribute = new Mage_Eav_Model_Entity_Attribute;
        $attribute->loadByCode('catalog_product', $data['create_select_installer']['code']);
        $collection = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection')
            ->setAttributeFilter($attribute->getId())
            ->load();
        $options = $collection->toOptionArray();
        $optionValueInstaller = $options[1]['value'];

        //add product attributes via api model
        $model = new Mage_Catalog_Model_Product_Attribute_Api();
        $response1 = $model->create($data['create_text_api']);
        $response2 = $model->create($data['create_select_api']);

        //add options
        $model = new Mage_Catalog_Model_Product_Attribute_Api();
        $model->addOption($response2, $data['create_select_api_options'][0]);
        $model->addOption($response2, $data['create_select_api_options'][1]);
        $options = $model->options($response2);
        $optionValueApi = $options[1]['value'];

        //add attributes to default attribute set via api model
        $model = new Mage_Catalog_Model_Product_Attribute_Set_Api();
        $model->attributeAdd($response1, 4);
        $model->attributeAdd($response2, 4);

        $attributes = array($response1, $response2);
        $this->setFixture('attributes', $attributes);

        return array($optionValueApi, $optionValueInstaller);
    }

    /**
     * Remove attributes created for tests
     *
     * Help CRUD method
     *
     * @return void
     */
    protected function _removeAttributes()
    {
        $attributes = $this->getFixture('attributes');
        foreach ($attributes as $attribute) {
            //remove by id
            $model = new Mage_Catalog_Model_Product_Attribute_Api();
            $model->remove($attribute);
        }

        $data = require dirname(__FILE__) . '/_fixture/ProductAttributeData.php';
        $attributes = array(
            $data['create_text_installer']['code'],
            $data['create_select_installer']['code'],
        );
        $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
        foreach ($attributes as $attribute) {
            //remove by code
            $installer->removeAttribute('catalog_product', $attribute);
        }
    }

    /**
     * Test create product with invalid attribute set
     *
     * @return void
     */
    public function testProductCreateWithInvalidAttributeSet()
    {
        $productData = require dirname(__FILE__) . '/_fixture/ProductData.php';
        $productData = $productData['create_full']['soap'];
        $productData['set'] = 'invalid';

        try {
            $this->call('catalog_product.create', $productData);
        } catch (Exception $e) {
            $this->assertEquals('Product attribute set is not existed', $e->getMessage(), 'Invalid exception message');
        }

        // find not product (category) attribute set identifier to try other error message
        /** @var $entity Mage_Eav_Model_Entity_Type */
        $entity = Mage::getModel('Mage_Eav_Model_Entity_Type');
        $entityTypeId = $entity->loadByCode('catalog_category')->getId();

        /** @var $attrSet Mage_Eav_Model_Entity_Attribute_Set */
        $attrSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set');

        /** @var $attrSetCollection Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
        $attrSetCollection = $attrSet->getCollection();
        $categoryAtrrSets  = $attrSetCollection->setEntityTypeFilter($entityTypeId)->toOptionHash();
        $categoryAttrSetId = key($categoryAtrrSets);

        $productData['set'] = $categoryAttrSetId;

        try {
            $this->call('catalog_product.create', $productData);
        } catch (Exception $e) {
            $this->assertEquals(
                'Product attribute set is not belong catalog product entity type',
                $e->getMessage(),
                'Invalid exception message'
            );
        }
    }

    /**
     * Test product attributes update in custom store view
     *
     * @return void
     */
    public function testProductUpdateCustomStore()
    {
        // Create test store view
        $website = Mage::app()->getWebsite();
        $this->_store = new Mage_Core_Model_Store();
        $this->_store->setData(array(
            'group_id' => $website->getDefaultGroupId(),
            'name' => 'Test Store View',
            'code' => 'test_store',
            'is_active' => true,
            'website_id' => $website->getId()
        ))->save();
        // We need to reinit stores config as we are going to load product models later in this test
        Mage::app()->reinitStores();
        $this->_getAppCache()->flush();

        $data = require dirname(__FILE__) . '/_fixture/ProductData.php';
        // create product for test
        $productId = $this->call('catalog_product.create', $data['create_full']['soap']);
        $this->assertGreaterThan(0, $productId, 'Product was not created');
        $this->setFixture('productId', $productId);

        // update product on test store
        $data['update_custom_store'] = array('productId' => $productId) + $data['update_custom_store'];
        $data['update_custom_store']['store'] = $this->_store->getCode();
        $isOk = $this->call('catalog_product.update', $data['update_custom_store']);
        $this->assertTrue($isOk, 'Can not update product on test store');

        // Load product in test store
        $product = new Mage_Catalog_Model_Product();
        $product->setStoreId($this->_store->getId())->load($productId);
        $this->assertNotNull($product->getId());
        $this->assertEquals($data['update_custom_store']['productData']['name'], $product->getName(),
            'Product name was not updated');

        // update product attribute in default store
        $data['update_default_store'] = array('productId' => $productId) + $data['update_default_store'];
        $isOk = $this->call('catalog_product.update', $data['update_default_store']);
        $this->assertTrue($isOk, 'Can not update product on default store');

        // Load product in default store
        $productDefault = new Mage_Catalog_Model_Product();
        $productDefault->load($productId);
        $this->assertEquals($data['update_default_store']['productData']['description'],
            $productDefault->getDescription(), 'Description attribute was not updated for default store');
        $this->assertEquals($data['create_full']['soap']['productData']['name'], $productDefault->getName(),
            'Product name attribute should not have been changed');

        // Load product in test store
        $productTestStore = new Mage_Catalog_Model_Product();
        $productTestStore->setStoreId($this->_store->getId())->load($productId);
        $this->assertEquals($data['update_default_store']['productData']['description'],
            $productTestStore->getDescription(), 'Description attribute was not updated for test store');
        $this->assertEquals($data['update_custom_store']['productData']['name'], $productTestStore->getName(),
            'Product name attribute should not have been changed for test store');
    }

    /**
     * Test create product to test default values for media attributes
     *
     * @return void
     */
    public function testProductCreateForTestMediaAttributesDefaultValue()
    {
        $productData = require dirname(__FILE__) . '/_fixture/ProductData.php';
        $productData = $productData['create'];

        // create product for test
        $productId = $this->call('catalog_product.create', $productData);
        $this->setFixture('productId', $productId);

        // test new product id returned
        $this->assertGreaterThan(0, $productId);

        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);

        $found = false;
        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $this->assertEquals($product->getData($mediaAttrCode), 'no_selection',
                'Attribute "' . $mediaAttrCode . '" has no default value');
            $found = true;
        }
        $this->assertTrue($found, 'Media attrributes not found');
    }
}
