<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Block_Widget_Grid_Massaction
 */
class Magento_Backend_Block_Widget_Grid_MassactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_Massaction
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_gridMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Factory_Helper
     */
    protected $_helperFactoryMock;

    protected function setUp()
    {
        $this->_gridMock = $this->getMock('Magento_Backend_Block_Widget_Grid', array('getId'), array(), '', false);
        $this->_gridMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('test_grid'));

        $this->_layoutMock = $this->getMock('Magento_Core_Model_Layout', array('getParentName', 'getBlock', 'helper'),
            array(), '', false, false
        );

        $this->_backendHelperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false);
        $this->_layoutMock->expects($this->any())
            ->method('helper')
            ->will($this->returnValue($this->_backendHelperMock));
        $this->_layoutMock->expects($this->any())
            ->method('getParentName')
            ->with('test_grid_massaction')
            ->will($this->returnValue('test_grid'));
        $this->_layoutMock->expects($this->any())
            ->method('getBlock')
            ->with('test_grid')
            ->will($this->returnValue($this->_gridMock));

        $this->_requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array('getParam'), array(), '',
            false
        );

        $this->_urlModelMock = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);

        $this->_helperFactoryMock = $this->getMock(
            'Magento_Core_Model_Factory_Helper', array('get'), array(), '', false, false
        );
        $this->_helperFactoryMock
            ->expects($this->any())
            ->method('get')
            ->with('Magento_Backend_Helper_Data')
            ->will($this->returnValue($this->_backendHelperMock));

        $arguments = array(
            'layout'       => $this->_layoutMock,
            'request'      => $this->_requestMock,
            'urlBuilder'   => $this->_urlModelMock,
            'helperFactory' => $this->_helperFactoryMock,
            'data'         => array(
                'massaction_id_field'  => 'test_id',
                'massaction_id_filter' => 'test_id'
            )
        );

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Magento_Backend_Block_Widget_Grid_Massaction', $arguments);
        $this->_block->setNameInLayout('test_grid_massaction');
    }

    protected function tearDown()
    {
        unset($this->_layoutMock);
        unset($this->_backendHelperMock);
        unset($this->_eventManagerMock);
        unset($this->_gridMock);
        unset($this->_urlModelMock);
        unset($this->_block);
    }

    public function testMassactionDefaultValues()
    {
        $this->assertEquals(0, $this->_block->getCount());
        $this->assertFalse($this->_block->isAvailable());

        $this->assertEquals('massaction', $this->_block->getFormFieldName());
        $this->assertEquals('internal_massaction', $this->_block->getFormFieldNameInternal());

        $this->assertEquals('test_grid_massactionJsObject', $this->_block->getJsObjectName());
        $this->assertEquals('test_gridJsObject', $this->_block->getGridJsObjectName());

        $this->assertEquals('test_grid_massaction', $this->_block->getHtmlId());
        $this->assertTrue($this->_block->getUseSelectAll());
    }

    /**
     * @param $itemId
     * @param $item
     * @param $expectedItem Magento_Object
     * @dataProvider itemsDataProvider
     */
    public function testItemsProcessing($itemId, $item, $expectedItem)
    {

        $this->_urlModelMock->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/index.php'));

        $urlReturnValueMap = array(
            array('*/*/test1', array(), 'http://localhost/index.php/backend/admin/test/test1'),
            array('*/*/test2', array(), 'http://localhost/index.php/backend/admin/test/test2')
        );
        $this->_urlModelMock->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap($urlReturnValueMap));

        $this->_block->addItem($itemId, $item);
        $this->assertEquals(1, $this->_block->getCount());

        $actualItem = $this->_block->getItem($itemId);
        $this->assertInstanceOf('Magento_Object', $actualItem);
        $this->assertEquals($expectedItem->getData(), $actualItem->getData());

        $this->_block->removeItem($itemId);
        $this->assertEquals(0, $this->_block->getCount());
        $this->assertNull($this->_block->getItem($itemId));
    }

    public function itemsDataProvider()
    {
        return array(
            array(
                'test_id1',
                array("label" => "Test Item One", "url" => "*/*/test1"),
                new Magento_Object(
                    array(
                        "label" => "Test Item One",
                        "url" => "http://localhost/index.php/backend/admin/test/test1",
                        "id" => 'test_id1',
                    )
                )
            ),
            array(
                'test_id2',
                new Magento_Object(
                    array(
                        "label" => "Test Item Two",
                        "url" => "*/*/test2"
                    )
                ),
                new Magento_Object(
                    array(
                        "label" => "Test Item Two",
                        "url" => "http://localhost/index.php/backend/admin/test/test2",
                        "id" => 'test_id2',
                    )
                )
            )
        );
    }

    /**
     * @param $param
     * @param $expectedJson
     * @param $expected
     * @dataProvider selectedDataProvider
     */
    public function testSelected($param, $expectedJson, $expected)
    {
        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->with($this->_block->getFormFieldNameInternal())
            ->will($this->returnValue($param));

        $this->assertEquals($expectedJson, $this->_block->getSelectedJson());
        $this->assertEquals($expected, $this->_block->getSelected());
    }

    public function selectedDataProvider()
    {
        return array(
            array(
                '',
                '',
                array()
            ),
            array(
                'test_id1,test_id2',
                'test_id1,test_id2',
                array('test_id1','test_id2')
            )
        );
    }

    public function testUseSelectAll()
    {
        $this->_block->setUseSelectAll(false);
        $this->assertFalse($this->_block->getUseSelectAll());

        $this->_block->setUseSelectAll(true);
        $this->assertTrue($this->_block->getUseSelectAll());
    }
}
