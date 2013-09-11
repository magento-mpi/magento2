<?php
/**
 * \Magento\Webhook\Model\Subscription\Options\Status
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Subscription_Options_StatusTest extends PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $translator = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Translate');
        $object = new \Magento\Webhook\Model\Subscription\Options\Status($translator);
        $expectedArray = array(
            \Magento\Webhook\Model\Subscription::STATUS_ACTIVE => 'Active',
            \Magento\Webhook\Model\Subscription::STATUS_REVOKED => 'Revoked',
            \Magento\Webhook\Model\Subscription::STATUS_INACTIVE => 'Inactive',
        );
        $this->assertEquals($expectedArray, $object->toOptionArray());

    }
}
