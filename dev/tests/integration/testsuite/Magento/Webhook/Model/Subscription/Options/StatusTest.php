<?php
/**
 * Magento_Webhook_Model_Subscription_Options_Status
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
        $translator = Mage::getObjectManager()->create('Magento_Core_Model_Translate');
        $object = new Magento_Webhook_Model_Subscription_Options_Status($translator);
        $expectedArray = array(
            Magento_Webhook_Model_Subscription::STATUS_ACTIVE => 'Active',
            Magento_Webhook_Model_Subscription::STATUS_REVOKED => 'Revoked',
            Magento_Webhook_Model_Subscription::STATUS_INACTIVE => 'Inactive',
        );
        $this->assertEquals($expectedArray, $object->toOptionArray());

    }
}
