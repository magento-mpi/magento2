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
            ->setMethods(array('createElement'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_blockFactoryMock  = $this->getMockBuilder('Magento\View\Element\BlockFactory')
            ->setMethods(array('createBlock'))
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\Core\Model\Layout',
            array(
                'structure' => $this->_structureMock,
                'blockFactory' => $this->_blockFactoryMock
            )
        );
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testCreateBlockException()
    {
        $this->_model->createBlock('type', 'blockname', array());
    }


    public function testCreateBlockSuccess()
    {
        $blockMock = $this->getMockBuilder('Magento\View\Element\AbstractBlock')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_blockFactoryMock->expects($this->once())
            ->method('createBlock')
            ->will($this->returnValue($blockMock));

        $this->_model->createBlock('type', 'blockname', array());
        $this->assertInstanceOf('Magento\View\Element\AbstractBlock', $this->_model->getBlock('blockname'));
    }
}
 