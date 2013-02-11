<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Product_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Product_SaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Product_SaveHandler($app, $objectManager);
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    /**
     * @dataProvider prepareDataDataProvider
     * @param array $data
     * @param array $expectedResult
     */
    public function testPrepareData(array $data, array $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_saveHandler->prepareData($data));
    }

    public function prepareDataDataProvider()
    {
        $data0 = array(
            'product' => array(
                Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD => 100500,
                'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                'name' => 'Tile Product',
                'sku' => 'tile_product',
                'description' => 'Product created via Product Tile',
                'short_description' => 'Product created via Product Tile',
                'weight' => 1,
                'news_to_date' => '',
                'news_from_date' => '',
                'url_key' => '',
                'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                'country_of_manufacture' => 'Albania',
                'quantity_and_stock_status' => array(
                    'qty' => 1000,
                    'is_in_stock' => 0,
                ),
                'is_returnable' => 2,
                'price' => 100,
                'tax_class_id' => 0,
                'stock_data' => array(
                    'manage_stock' => 1,
                    'qty' => 500,
                    'is_in_stock' => 1,
                )
            )
        );
        $preparedData0 = $data0;
        unset($preparedData0['product'][Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD]);
        // override stock data by values from 'quantity_and_stock_status' attribute
        $preparedData0['product']['stock_data'] = array(
            'qty' => 1000,
            'is_in_stock' => 0,
            'manage_stock' => 1, // if user specifies quantity then 'Manage Stock' is set to 'Yes' automatically
            'use_config_manage_stock' => 0, // manage stock explicitly
            'is_qty_decimal' => 0, // quantity can be represented only by integer value
        );
        return array(
            array($data0, $preparedData0)
        );
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Product data is invalid.
     */
    public function testPrepareDataThrowsExceptionWhenRequestDataIsInvalid()
    {
        $this->_saveHandler->prepareData(array());
    }

    /**
     * @dataProvider prepareDataDataProvider
     * @param array $data
     * @param array $preparedData
     */
    public function testSave(array $data, array $preparedData)
    {
        $saveHandler = $this->getSaveHandlerInstanceForSaveMethodTest($preparedData);
        $saveHandler->save($data);
    }

    /**
     * @dataProvider prepareDataDataProvider
     * @param array $data
     * @param array $preparedData
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Product data is invalid.
     */
    public function testSaveThrowsExceptionWhenProductDataValidationFails(array $data, array $preparedData)
    {
        $saveHandler = $this->getSaveHandlerInstanceForSaveMethodTest($preparedData, false);
        $saveHandler->save($data);
    }

    /**
     * Retrieve save handler instance for save method test
     *
     * @param array $preparedData
     * @param bool $isDataValid Mage_Catalod_Model_Product::validate() method call result
     * @return Mage_Launcher_Model_Storelauncher_Product_SaveHandler
     */
    public function getSaveHandlerInstanceForSaveMethodTest(array $preparedData, $isDataValid = true)
    {
        $websiteId = 1;

        // mock application instance
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $website = $this->getMock('Mage_Core_Model_Website', array('getId'), array(), '', false);
        $website->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($websiteId));
        $store = $this->getMock('Mage_Core_Model_Store', array('getWebsite'), array(), '', false);
        $store->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($website));
        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        // mock product instance
        $product = $this->getMock('Mage_Catalog_Model_Product',
            array(
                'save', 'setStoreId', 'setTypeId', 'addData', 'setData', 'setAttributeSetId', 'setWebsiteIds',
                'validate',
            ),
            array(),
            '',
            false
        );
        $product->expects($this->once())
            ->method('setStoreId')
            ->with(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('validate')
            ->will($this->returnValue($isDataValid));
        $product->expects($this->once())
            ->method('setWebsiteIds')
            ->with(array($websiteId))
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('setTypeId')
            ->with(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('setData')
            ->with('_edit_mode', true)
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('addData')
            ->with($preparedData['product'])
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('setAttributeSetId')
            ->will($this->returnValue($product));

        $saveExpectation = ($isDataValid) ? $this->once() : $this->never();
        // save method can be called only if validation have passed successfully
        $product->expects($saveExpectation)
            ->method('save')
            ->will($this->returnValue($product));

        // mock object manager instance
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Mage_Catalog_Model_Product', array(), false)
            ->will($this->returnValue($product));
        return new Mage_Launcher_Model_Storelauncher_Product_SaveHandler($app, $objectManager);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Product data is invalid.
     */
    public function testSaveDoesNotCatchExceptionThrownByPrepareData()
    {
        $this->_saveHandler->save(array());
    }
}
