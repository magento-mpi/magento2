<?php
/**
 * Mage_Webhook_Model_Subscription_Options_Status
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Subscription_Options_StatusTest extends PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $webhookHelper = Mage::getObjectManager()->create('Mage_Webhook_Helper_Data');
        $object = new Mage_Webhook_Model_Subscription_Options_Status($webhookHelper);
        $expectedArray = array(
            Mage_Webhook_Model_Subscription::STATUS_ACTIVE => 'Active',
            Mage_Webhook_Model_Subscription::STATUS_REVOKED => 'Revoked',
            Mage_Webhook_Model_Subscription::STATUS_INACTIVE => 'Inactive',
        );
        $this->assertEquals($expectedArray, $object->toOptionArray());

    }
}