<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model;

/**
 * \Magento\Webhook\Model\User
 *
 * @magentoDbIsolation enabled
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSharedSecret()
    {
        $webapiUserId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webapi\Model\Acl\User')
            ->setSecret('secret')
            ->save()
            ->getId();
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\User', array('webapiUserId' => $webapiUserId));
        $this->assertEquals('secret', $user->getSharedSecret());
    }
}
