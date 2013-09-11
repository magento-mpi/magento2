<?php
/**
 * \Magento\Webhook\Model\User\Factory
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

        $factory = new \Magento\Webhook\Model\User\Factory($mockObjectManager);

        $mockUser = $this->getMockBuilder('Magento\Webhook\Model\User')
            ->disableOriginalConstructor()
            ->getMock();

        $webapiUserId = 'userId';

        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Webhook\Model\User', array('webapiUserId' => $webapiUserId))
            ->will($this->returnValue($mockUser));

        $this->assertSame($mockUser, $factory->create($webapiUserId));
    }

}
