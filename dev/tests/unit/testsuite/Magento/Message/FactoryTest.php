<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\Factory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;


    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager', array(), array(), '', false);
        $this->factory = new \Magento\Message\Factory(
            $this->objectManagerMock
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong message type
     */
    public function testCreateWithWrongTypeException()
    {
        $this->objectManagerMock->expects($this->never())->method('create');
        $this->factory->create('type', 'text');
    }

    /**
     * @param string $type
     * @dataProvider createWithWrongInterfaceImplementationDataProvider
     */
    public function testCreateWithWrongInterfaceImplementation($type)
    {

        $className = 'Magento\Message\\' . ucfirst($type);
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($className, array('text' => 'text'));
        $this->setExpectedException('\InvalidArgumentException',
            $className . ' doesn\'t implement \Magento\Message\MessageInterface');
        $this->factory->create($type, 'text');
    }

    public function createWithWrongInterfaceImplementationDataProvider()
    {
        return array(
            MessageInterface::TYPE_ERROR => array(MessageInterface::TYPE_ERROR),
            MessageInterface::TYPE_WARNING => array(MessageInterface::TYPE_WARNING),
            MessageInterface::TYPE_NOTICE => array(MessageInterface::TYPE_NOTICE),
            MessageInterface::TYPE_SUCCESS => array(MessageInterface::TYPE_SUCCESS)
        );
    }
}
