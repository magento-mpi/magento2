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
     * @var Mage_Backend_Helper_Data
     */
    protected $_backendHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_gridMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_gridColumnSetMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    protected function setUp()
    {
        $this->_gridMock = $this->getMockBuilder('Mage_Backend_Block_Widget_Grid')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();
        $this->_gridMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('test_grid'));

        $this->_backendHelper = $this->getMock(
            'Mage_Backend_Helper_Data', array('jsQuoteEscape', '__'), array(), '', false, false
        );

        $this->_blockMock = $this->getMockBuilder('Mage_Backend_Block_Widget_Grid_Massaction')
            ->setConstructorArgs(array(array('helper' => $this->_backendHelper)))
            ->setMethods(array('getParentBlock', 'getRequest'))
            ->getMock();
        $this->_blockMock->expects($this->any())
            ->method('getParentBlock')
            ->will($this->returnValue($this->_gridMock));
    }

    protected function tearDown()
    {
        unset($this->_gridMock);
        unset($this->_blockMock);
        unset($this->_backendHelper);
    }

    public function testMassactionDefaultValues()
    {
        $block = new Mage_Backend_Block_Widget_Grid_Massaction(
            array(
                'helper' => $this->_backendHelper
            )
        );
        $this->assertEquals(0, $block->getCount());
        $this->assertFalse($block->isAvailable());

        $this->assertEquals('massaction', $block->getFormFieldName());
        $this->assertEquals('internal_massaction', $block->getFormFieldNameInternal());
    }

    public function testItemsProcessing()
    {
        /** @var $block Mage_Backend_Block_Widget_Grid_Massaction */
        $this->_block = new Mage_Backend_Block_Widget_Grid_Massaction(
            array(
                'helper' => $this->_backendHelper,
                'massaction_id_field' => 'test_id',
                'massaction_id_filter' => 'test_id',
                'form_field_name' => 'test_form',
                'use_select_all' => true,
                'options' => array(
                    'test_id1' => array(
                        'label' => 'Test One',
                        'url' => '*/*/test1'
                    ),
                    'test_id2' => array(
                        'label' => 'Test Two',
                        'url' => '*/*/test2'
                    ),
                )
            )
        );

        $item = array( "label" => "Test Item", "url" => "*/*/test");

        $this->_block->addItem("test_id3", $item);
        $item['id'] = 'test_id4';
        $this->_block->addItem("test_id4", new Varien_Object($item));

        $this->assertEquals(4, $this->_block->getCount());
        $this->assertInstanceOf('Varien_Object', $this->_block->getItem('test_id3'));

        $item['id'] = 'test_id3';
        $itemExpected = new Varien_Object($item);
        $this->assertEquals($itemExpected, $this->_block->getItem('test_id3'));

        $this->_block->removeItem('test_id4');
        $this->assertEquals(3, $this->_block->getCount());

        $this->_block->removeItem('test_id3');
        $this->assertEquals(2, $this->_block->getCount());
    }

    public function testHtmlId()
    {
        $this->assertEquals('test_grid_massaction', $this->_blockMock->getHtmlId());
    }

    public function testJsObjectName()
    {
        $this->assertEquals('test_grid_massactionJsObject', $this->_blockMock->getJsObjectName());
    }

    public function testGridJsObjectName()
    {
        $this->assertEquals('test_gridJsObject', $this->_blockMock->getGridJsObjectName());
    }

    /**
     * @param $param
     * @param $expectedJson
     * @param $expected
     * @dataProvider selectedDataProvider
     */
    public function testSelected($param, $expectedJson, $expected)
    {
        $requestMock = $this->getMockBuilder('Magento_Test_Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam'))
            ->getMock();
        $requestMock->expects($this->any())
            ->method('getParam')
            ->with($this->_blockMock->getFormFieldNameInternal())
            ->will($this->returnValue($param));

        $this->_blockMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        $this->assertEquals($expectedJson, $this->_blockMock->getSelectedJson());
        $this->assertEquals($expected, $this->_blockMock->getSelected());
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
        $this->assertTrue($this->_blockMock->getUseSelectAll());

        $this->_blockMock->setUseSelectAll(false);
        $this->assertFalse($this->_blockMock->getUseSelectAll());

        $this->_blockMock->setUseSelectAll(true);
        $this->assertTrue($this->_blockMock->getUseSelectAll());
    }
}
