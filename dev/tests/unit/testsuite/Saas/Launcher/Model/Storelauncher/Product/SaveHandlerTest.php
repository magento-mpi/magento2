<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Product_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Storelauncher_Product_SaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_saveHandler = new Saas_Launcher_Model_Storelauncher_Product_SaveHandler($app, $objectManager);
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
                Magento_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD => 100500,
                'status' => Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
                'name' => 'Tile Product',
                'sku' => 'tile_product',
                'description' => 'Product created via Product Tile',
                'short_description' => 'Product created via Product Tile',
                'weight' => 1,
                'news_to_date' => '',
                'news_from_date' => '',
                'url_key' => '',
                'visibility' => Magento_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
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
                ),
                'is_virtual' => null
            )
        );
        $preparedData0 = $data0;
        unset($preparedData0['product'][Magento_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD]);
        // override stock data by values from 'quantity_and_stock_status' attribute
        $preparedData0['product']['stock_data'] = array(
            'qty' => 1000,
            'is_in_stock' => 0,
            'manage_stock' => 1, // if user specifies quantity then 'Manage Stock' is set to 'Yes' automatically
            'use_config_manage_stock' => 0, // manage stock explicitly
            'is_qty_decimal' => 0, // quantity can be represented only by integer value
        );
        $preparedData0['product']['typeId'] = Magento_Catalog_Model_Product_Type::TYPE_SIMPLE;

        // add virtual product test data
        $data1 = $data0;
        $data1['product']['is_virtual'] = '';
        $preparedData1 = $preparedData0;
        $preparedData1['product']['typeId'] = Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL;
        return array(
            array($data0, $preparedData0),
            array($data1, $preparedData1),
        );
    }

    /**
     * @expectedException Saas_Launcher_Exception
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
     * @expectedException Saas_Launcher_Exception
     * @expectedExceptionMessage Product data is invalid:
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
     * @return Saas_Launcher_Model_Storelauncher_Product_SaveHandler
     */
    public function getSaveHandlerInstanceForSaveMethodTest(array $preparedData, $isDataValid = true)
    {
        $websiteId = 1;

        // mock application instance
        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $website = $this->getMock('Magento_Core_Model_Website', array('getId'), array(), '', false);
        $website->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($websiteId));
        $store = $this->getMock('Magento_Core_Model_Store', array('getWebsite'), array(), '', false);
        $store->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($website));
        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        // mock product instance
        $product = $this->getMock('Magento_Catalog_Model_Product',
            array(
                'save', 'setStoreId', 'setTypeId', 'addData', 'setData', 'setAttributeSetId', 'setWebsiteIds',
                'validate', 'getDefaultAttributeSetId'
            ),
            array(),
            '',
            false
        );
        $product->expects($this->once())
            ->method('setStoreId')
            ->with(Magento_Core_Model_App::ADMIN_STORE_ID)
            ->will($this->returnValue($product));
        if ($isDataValid) {
            $product->expects($this->once())
                ->method('validate')
                ->will($this->returnValue($isDataValid));
        } else {
            $product->expects($this->once())
                ->method('validate')
                ->will($this->throwException(new Saas_Launcher_Exception('Product data is invalid:')));
        }
        $product->expects($this->once())
            ->method('setWebsiteIds')
            ->with(array($websiteId))
            ->will($this->returnValue($product));
        $product->expects($this->once())
            ->method('setTypeId')
            ->with($preparedData['product']['typeId'])
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
            ->method('getDefaultAttributeSetId')
            ->will($this->returnValue(4));
        $product->expects($this->once())
            ->method('setAttributeSetId')
            ->with(4)
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
            ->with('Magento_Catalog_Model_Product', array())
            ->will($this->returnValue($product));
        return new Saas_Launcher_Model_Storelauncher_Product_SaveHandler($app, $objectManager);
    }

    /**
     * @expectedException Saas_Launcher_Exception
     * @expectedExceptionMessage Product data is invalid.
     */
    public function testSaveDoesNotCatchExceptionThrownByPrepareData()
    {
        $this->_saveHandler->save(array());
    }
}
