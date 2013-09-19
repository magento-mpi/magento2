<?php
/**
 * \Magento\Webhook\Model\Webapi\EventHandler\Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Webapi\EventHandler;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $_mockObjectManager;

    /** @var \Magento\Webhook\Model\Webapi\EventHandler\Factory */
    private $_factory;

    public function setUp()
    {
        $this->_mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new \Magento\Webhook\Model\Webapi\EventHandler\Factory($this->_mockObjectManager);
    }

    public function testCreate()
    {
        $mockEntity = $this->getMockBuilder('Magento\Webhook\Model\Webapi\EventHandler')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Webhook\Model\Webapi\EventHandler'), $this->equalTo(array()))
            ->will($this->returnValue($mockEntity));
        $this->assertSame($mockEntity, $this->_factory->create());
    }
}
