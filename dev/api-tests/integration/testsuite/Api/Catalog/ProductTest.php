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
 */
class Api_Catalog_ProductTest extends Magento_Test_Webservice
{

    /**
     * Test store model
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

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
     * Test for set special price for product
     */
    public function testSetSpecialPrice()
    {
        $productData  = require dirname(__FILE__) . '/_fixtures/ProductData.php';
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
        $data = require dirname(__FILE__) . '/_fixtures/ProductData.php';

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
        $data = require dirname(__FILE__) . '/_fixtures/ProductData.php';

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
        $data = require dirname(__FILE__) . '/_fixtures/ProductData.php';

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

        $data = require dirname(__FILE__) . '/_fixtures/ProductAttributeData.php';


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

        $data = require dirname(__FILE__) . '/_fixtures/ProductAttributeData.php';
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
        $productData = require dirname(__FILE__) . '/_fixtures/ProductData.php';
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

        $data = require dirname(__FILE__) . '/_fixtures/ProductData.php';
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
        $productData = require dirname(__FILE__) . '/_fixtures/ProductData.php';
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
