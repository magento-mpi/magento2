<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Event;

/**
 * Class WrapperFactoryTest
 *
 * @package Magento\Framework\Event
 */
class WrapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $expectedInstance = 'Magento\Framework\Event\Observer';
        $objectManagerMock = $this->getMock('\Magento\Framework\ObjectManagerInterface');

        $wrapperFactory = new WrapperFactory($objectManagerMock);
        $arguments = ['argument' => 'value', 'data' => 'data'];
        $observerInstanceMock = $this->getMock($expectedInstance);

        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with($expectedInstance, $arguments)
            ->will($this->returnValue($observerInstanceMock));

        $this->assertInstanceOf($expectedInstance, $wrapperFactory->create($arguments));
    }

}
