<?php
/**
 * Magento_Webhook_Model_User_Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_User_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $mockObjectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Magento_Webhook_Model_User_Factory($mockObjectManager);

        $mockUser = $this->getMockBuilder('Magento_Webhook_Model_User')
            ->disableOriginalConstructor()
            ->getMock();

        $webapiUserId = 'userId';

        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Webhook_Model_User', array('webapiUserId' => $webapiUserId))
            ->will($this->returnValue($mockUser));

        $this->assertSame($mockUser, $factory->create($webapiUserId));
    }

}
