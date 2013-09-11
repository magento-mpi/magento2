<?php
/**
 * \Magento\Webhook\Model\User
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_UserTest extends PHPUnit_Framework_TestCase
{
    public function testGetSharedSecret()
    {
        $webapiUserId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Webapi\Model\Acl\User')
            ->setSecret('secret')
            ->save()
            ->getId();
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\User', array('webapiUserId' => $webapiUserId));
        $this->assertEquals('secret', $user->getSharedSecret());
    }
}
