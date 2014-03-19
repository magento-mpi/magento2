<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Placeholder\Mapper
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock(
            'Magento\FullPageCache\Model\Container\PlaceholderFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_configMock = $this->getMock('Magento\FullPageCache\Model\Placeholder\ConfigInterface');
        $this->_model = new \Magento\FullPageCache\Model\Placeholder\Mapper($this->_factoryMock, $this->_configMock);
        $this->_blockMock = $this->getMock(
            'Magento\View\Element\Template',
            array('getType', 'getNameInLayout', 'getCacheKey', 'getCacheKeyInfo'),
            array(),
            '',
            false
        );
        $this->_blockMock->expects($this->any())->method('getType')->will($this->returnValue('testBlockType'));
        $this->_blockMock->expects($this->any())->method('getNameInLayout')->will($this->returnValue('testBlockName'));
        $this->_blockMock->expects(
            $this->any()
        )->method(
            'getCacheKeyInfo'
        )->will(
            $this->returnValue(array('someKey' => 'someValue'))
        );
        $this->_blockMock->expects($this->any())->method('getCacheKey')->will($this->returnValue('someCacheKey'));
    }

    public function testMapWithoutPlaceholders()
    {
        $this->_configMock->expects(
            $this->once()
        )->method(
            'getPlaceholders'
        )->with(
            'testBlockType'
        )->will(
            $this->returnValue(array())
        );
        $this->assertNull($this->_model->map($this->_blockMock));
    }

    public function testMapWithDifferentNamesOfPlaceholderBlock()
    {
        $data = array(array('name' => 'someBlockName'));
        $this->_configMock->expects(
            $this->once()
        )->method(
            'getPlaceholders'
        )->with(
            'testBlockType'
        )->will(
            $this->returnValue($data)
        );
        $this->assertNull($this->_model->map($this->_blockMock));
    }

    public function testMapWithEqualsNamesOfPlaceholderBlock()
    {
        $data = array(array('name' => 'testBlockName', 'code' => 'testCode', 'container' => 'testContainer'));

        $this->_configMock->expects(
            $this->once()
        )->method(
            'getPlaceholders'
        )->with(
            'testBlockType'
        )->will(
            $this->returnValue($data)
        );

        $expected = 'testCode container="testContainer" block="' . get_class(
            $this->_blockMock
        ) . '" cache_id="someCacheKey" someKey="someValue"';

        $this->_factoryMock->expects($this->once())->method('create')->with($expected);
        $this->_model->map($this->_blockMock);
    }
}
