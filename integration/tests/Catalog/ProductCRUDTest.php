<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Product CRUD operations
 */
class Catalog_ProductCRUDTest extends Magento_Test_Webservice
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down
     * 
     * @return void
     */
    protected function tearDown()
    {
        if ($this->getFixture('attributes')) {
            $this->_removeAttributes();
        }
        
        $product = new Mage_Catalog_Model_Product();
        $product->load($this->getFixture('productId'));
        if ($product->getId()) {
            Mage::register('isSecureArea', true);
            $product->delete();
            Mage::unregister('isSecureArea');
        }

        parent::tearDown();
    }

    /**
     * Test product CRUD
     *
     * @return void
     */
    public function _testProductCRUD()
    {
        $data = require dirname(__FILE__).'/_fixtures/ProductData.php';

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
        $data['update']['product'] = $productId;
        $isOk = $this->call('catalog_product.update', $data['update']);

        //test call response is true
        $this->assertTrue($isOk, 'Call returned false');

        //test product exists in DB after update and product data changed
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNotNull($product->getId());
        $this->assertEquals($data['update']['productData']['name'], $product->getName());

        //delete product
        $isOk = $this->call('catalog_product.delete', array($productId));

        //test call response is true
        $this->assertTrue((bool)$isOk, 'Call returned false');  //in SOAP v2 it's integer:1
        
        //test product not exists in DB after delete
        $product = new Mage_Catalog_Model_Product();
        $product->load($productId);
        $this->assertNull($product->getId());
    }

    public function testProductWithOptionsCRUD()
    {
        list($optionValueApi, $optionValueInstaller) = $this->_addAttributes();
        $data = require dirname(__FILE__).'/_fixtures/ProductData.php';

        switch (TESTS_WEBSERVICE_TYPE) {
            case self::TYPE_SOAPV1:
                $this->_testSoapV1($optionValueApi, $optionValueInstaller, $data);
                break;

            case self::TYPE_SOAPV2:
                $this->_testSoapV2($optionValueApi, $optionValueInstaller, $data);
                break;
        }
    }

    public function _testSoapV1($optionValueApi, $optionValueInstaller, $data)
    {
        $attributes = $data['create_with_attributes_soap']['productData']['additional_attributes'];
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

    public function _testSoapV2($optionValueApi, $optionValueInstaller, $data)
    {
        $attributes = $data['create_with_attributes_soapv2']['productData']['additional_attributes'];
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
     * @return array
     */
    protected function _addAttributes()
    {
        $this->setFixture('attributes', true);

        $data = require dirname(__FILE__).'/_fixtures/ProductAttributeData.php';


        // add product attributes via installer
        $installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
        $installer->addAttribute('catalog_product', $data['create_text_installer']['code'],
            $data['create_text_installer']['attributeData']);
        $installer->addAttribute('catalog_product', $data['create_select_installer']['code'],
            $data['create_select_installer']['attributeData']);

        //add attributes to default attribute set via installer
        $installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_text_installer']['code']);
        $installer->addAttributeToSet('catalog_product', 4, 'Default', $data['create_select_installer']['code']);

        $attribute = new Mage_Eav_Model_Entity_Attribute;
        $attribute->loadByCode('catalog_product', $data['create_select_installer']['code']);
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
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
     * @return void
     */
    protected function _removeAttributes()
    {
        $attributes = $this->getFixture('attributes');
        foreach ($attributes as $attribute) {
            //remove by id
            $model = new Mage_Catalog_Model_Product_Attribute_Api();
            $response = $model->remove($attribute);
        }

        $data = require dirname(__FILE__).'/_fixtures/ProductAttributeData.php';
        $attributes = array(
            $data['create_text_installer']['code'],
            $data['create_select_installer']['code'],
        );
        $installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
        foreach ($attributes as $attribute) {
            //remove by code
            $installer->removeAttribute('catalog_product', $attribute);
        }
    }
}
