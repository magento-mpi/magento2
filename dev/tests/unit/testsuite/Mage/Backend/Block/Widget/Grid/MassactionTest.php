<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget_Grid_Massaction
 */
class Mage_Backend_Block_Widget_Grid_MassactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Massaction
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

    protected function setUp()
    {
        $this->_gridMock = $this->getMockBuilder('Mage_Backend_Block_Widget_Grid')
            ->setMethods(array('getId'))
            ->getMock();
        $this->_gridMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('test_grid'));

        $this->_layoutMock = $this->getMockBuilder('Mage_Core_Model_Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_layoutMock->expects($this->any())
            ->method('getParentName')
            ->with('test_grid_massaction')
            ->will($this->returnValue('test_grid'));
        $this->_layoutMock->expects($this->any())
            ->method('getBlock')
            ->with('test_grid')
            ->will($this->returnValue($this->_gridMock));

        $this->_eventManagerMock = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_urlModelMock = $this->getMockBuilder('Mage_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_requestMock = $this->getMockBuilder('Zend_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_backendHelperMock = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_block = new Mage_Backend_Block_Widget_Grid_Massaction(
            array(
                'eventManager' => $this->_eventManagerMock,
                'layout' => $this->_layoutMock,
                'urlModel' => $this->_urlModelMock,
                'helper' => $this->_backendHelperMock,
                'request' => $this->_requestMock,
                'massaction_id_field' => 'test_id',
                'massaction_id_filter' => 'test_id'
            )
        );
        $this->_block->setNameInLayout('test_grid_massaction');
    }

    protected function tearDown()
    {
        unset($this->_block);
        unset($this->_gridMock);
        unset($this->_urlModelMock);
        unset($this->_layoutMock);
        unset($this->_eventManagerMock);
        unset($this->_backendHelperMock);
        unset($this->_requestMock);
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

    public function testItemsProcessing()
    {
        $this->_urlModelMock->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/index.php'));

        $urlReturnValueMap = array(
            array('*/*/test1', 'http://localhost/index.php/backend/admin/test/test1'),
            array('*/*/test2', 'http://localhost/index.php/backend/admin/test/test2')
        );
        $this->_urlModelMock->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap($urlReturnValueMap));

        $item1 = array("label" => "Test Item One", "url" => "*/*/test1");
        $this->_block->addItem("test_id1", $item1);
        $this->assertEquals(1, $this->_block->getCount());

        $expectedItem1 = new Varien_Object(
            array(
                "label" => "Test Item One",
                "url" => "*/*/test1",
                "id" => 'test_id1',
            )
        );
        $expectedItem1->setUrl($this->_block->getUrl($expectedItem1->getUrl()));

        $actualItem1 = $this->_block->getItem('test_id1');
        $this->assertInstanceOf('Varien_Object', $actualItem1);
        $this->assertEquals($expectedItem1, $actualItem1);

        $item2 = new Varien_Object(array("label" => "Test Item Two", "url" => "*/*/test2"));
        $this->_block->addItem("test_id2", $item2);

        $this->assertEquals(2, $this->_block->getCount());
        $this->assertInstanceOf('Varien_Object', $this->_block->getItem('test_id2'));

        $expectedItem2 = $item2;
        $expectedItem2->setId('test_id2')->setUrl($this->_block->getUrl('*/*/test2'));
        $this->assertEquals($expectedItem2, $this->_block->getItem('test_id2'));

        $items = array('test_id1' => $expectedItem1, 'test_id2' => $expectedItem2);
        $this->assertEquals($items, $this->_block->getItems());

        $this->_block->removeItem('test_id2');
        $this->assertEquals(1, $this->_block->getCount());

        $this->_block->removeItem('test_id1');
        $this->assertEquals(0, $this->_block->getCount());
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
