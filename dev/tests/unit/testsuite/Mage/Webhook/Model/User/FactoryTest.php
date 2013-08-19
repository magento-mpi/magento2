<?php
/**
 * Mage_Webhook_Model_User_Factory
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_User_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new Mage_Webhook_Model_User_Factory($mockObjectManager);

        $mockUser = $this->getMockBuilder('Mage_Webhook_Model_User')
            ->disableOriginalConstructor()
            ->getMock();

        $webapiUserId = 'userId';

        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with('Mage_Webhook_Model_User', array('webapiUserId' => $webapiUserId))
            ->will($this->returnValue($mockUser));

        $this->assertSame($mockUser, $factory->create($webapiUserId));
    }

}
