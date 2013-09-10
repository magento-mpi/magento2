<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_MapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Placeholder_Mapper
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock('Magento_FullPageCache_Model_Container_PlaceholderFactory',
            array(), array(), '', false
        );
        $this->_configMock = $this->getMock('Magento_FullPageCache_Model_Placeholder_ConfigInterface');
        $this->_model = new Magento_FullPageCache_Model_Placeholder_Mapper($this->_factoryMock, $this->_configMock);
        $this->_blockMock = $this->getMock('Magento_Core_Block_Template',
            array('getType', 'getNameInLayout', 'getCacheKey', 'getCacheKeyInfo'),
            array(), '', false
        );
        $this->_blockMock->expects($this->any())->method('getType')->will($this->returnValue('testBlockType'));
        $this->_blockMock->expects($this->any())->method('getNameInLayout')->will($this->returnValue('testBlockName'));
        $this->_blockMock->expects($this->any())->method('getCacheKeyInfo')
            ->will($this->returnValue(array('someKey' => 'someValue')));
        $this->_blockMock->expects($this->any())->method('getCacheKey')->will($this->returnValue('someCacheKey'));
    }

    public function testMapWithoutPlaceholders()
    {
        $this->_configMock->expects($this->once())->method('getPlaceholders')
            ->with('testBlockType')->will($this->returnValue(array()));
        $this->assertNull($this->_model->map($this->_blockMock));
    }

    public function testMapWithDifferentNamesOfPlaceholderBlock()
    {
        $data = array(array('name' => 'someBlockName'));
        $this->_configMock->expects($this->once())->method('getPlaceholders')
            ->with('testBlockType')->will($this->returnValue($data));
        $this->assertNull($this->_model->map($this->_blockMock));
    }

    public function testMapWithEqualsNamesOfPlaceholderBlock()
    {
        $data = array(
            array(
                'name' => 'testBlockName',
                'code' => 'testCode',
                'container' => 'testContainer',
            )
        );

        $this->_configMock->expects($this->once())->method('getPlaceholders')
            ->with('testBlockType')->will($this->returnValue($data));

        $expected =  'testCode container="testContainer" block="'
            . get_class($this->_blockMock) . '" cache_id="someCacheKey" someKey="someValue"';

        $this->_factoryMock->expects($this->once())->method('create')->with($expected);
        $this->_model->map($this->_blockMock);
    }
}
