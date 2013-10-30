<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data;

/**
 * Tests for \Magento\Data\Form\Factory
 */
class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array(), array(), '', false);
        $this->_sessionMock = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage WrongClass doesn't extends \Magento\Data\Form
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';

        $formMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($formMock));

        $formFactory = new FormFactory($this->_objectManagerMock, $this->_sessionMock, $className);
        $formFactory->create();
    }

    public function testCreate()
    {
        $className = 'Magento\Data\Form';
        $formMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($className)
            ->will($this->returnValue($formMock));
        $formMock->expects($this->once())
            ->method('setSession')
            ->with($this->_sessionMock);

        $formFactory = new FormFactory($this->_objectManagerMock, $this->_sessionMock, $className);
        $this->assertSame($formMock, $formFactory->create());
    }
}
