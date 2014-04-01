<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Data;

/**
 * Tests for \Magento\Data\FormFactory
 */
class FormFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array(), array(), '', false);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage WrongClass doesn't extend \Magento\Data\Form
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';

        $formMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($formMock));

        $formFactory = new FormFactory($this->_objectManagerMock, $className);
        $formFactory->create();
    }

    public function testCreate()
    {
        $className = 'Magento\Data\Form';
        $formMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className
        )->will(
            $this->returnValue($formMock)
        );

        $formFactory = new FormFactory($this->_objectManagerMock, $className);
        $this->assertSame($formMock, $formFactory->create());
    }
}
