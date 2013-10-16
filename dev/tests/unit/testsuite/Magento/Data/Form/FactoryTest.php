<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Form;

/**
 * Tests for \Magento\Data\Form\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager',
            array('create'), array(), '', false);
        $this->_sessionMock = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
        $this->_factory = new Factory($this->_objectManagerMock, $this->_sessionMock);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento\ObjectManager\ObjectManager', '_objectManager', $this->_factory);
        $this->assertAttributeInstanceOf('Magento\Core\Model\Session\AbstractSession', '_session', $this->_factory);
    }

    public function testCreate()
    {
        $className = 'Magento\Data\Form';
        $formMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className, array())
            ->will($this->returnValue($formMock));
        $formMock->expects($this->once())
            ->method('setSession')
            ->with($this->_sessionMock)
            ->will($this->returnSelf());

        $this->assertSame($formMock, $this->_factory->create());
    }
}
