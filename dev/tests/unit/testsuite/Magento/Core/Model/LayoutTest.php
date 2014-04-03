<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockFactoryMock;

    protected function setUp()
    {
        $this->_structureMock = $this->getMockBuilder('Magento\Data\Structure')
            ->setMethods(['createElement'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_blockFactoryMock = $this->getMockBuilder('Magento\View\Element\BlockFactory')
            ->setMethods(['createBlock'])
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\Core\Model\Layout',
            array('structure' => $this->_structureMock, 'blockFactory' => $this->_blockFactoryMock)
        );
    }

    /**
     * @expectedException \Magento\Model\Exception
     */
    public function testCreateBlockException()
    {
        $this->_model->createBlock('type', 'blockname', array());
    }

    /**
     * Test _getBlockInstance() with Exception
     *
     * @expectedException \Magento\Model\Exception
     * @expectedExceptionMessage Invalid block type: test.block
     */
    public function testGetBlockInstanceException()
    {
        $type = 'test.block';
        $name = 'test-block';
        $attributes = [];

        $this->_structureMock->expects($this->once())
            ->method('createElement')
            ->with($this->equalTo($name), $this->equalTo(['type' => 'block']));
        $this->_blockFactoryMock->expects($this->once())
            ->method('createBlock')
            ->with($this->equalTo($type), $this->equalTo($attributes))
            ->will($this->throwException(new \ReflectionException('Exception during block creating')));
        $result = $this->_model->createBlock($type, $name, $attributes);
        $this->assertNull($result);
    }

    public function testCreateBlockSuccess()
    {
        $blockMock = $this->getMockBuilder(
            'Magento\View\Element\AbstractBlock'
        )->disableOriginalConstructor()->getMockForAbstractClass();
        $this->_blockFactoryMock->expects($this->once())->method('createBlock')->will($this->returnValue($blockMock));

        $this->_model->createBlock('type', 'blockname', array());
        $this->assertInstanceOf('Magento\View\Element\AbstractBlock', $this->_model->getBlock('blockname'));
    }

    public function testIsCacheable()
    {
        $xpath = '/block[@cacheable="false"]';
        $dom = new \DOMDocument();
        $parent = $dom->createElement('parent');
        $parent->setAttribute('xpath', $xpath);
        $dom->appendChild($parent);

        $expected = true;
        $result = $this->_model->isCacheable();
        $this->assertEquals($expected, $result);
    }
}
