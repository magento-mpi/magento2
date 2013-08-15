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
        $translatorMock = $this->getMockBuilder('Mage_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $translatorMock->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translateCallback')));
        $object = new Mage_Webhook_Model_Subscription_Options_Status($translatorMock);
        $expectedArray = array(
            Mage_Webhook_Model_Subscription::STATUS_ACTIVE => 'Active',
            Mage_Webhook_Model_Subscription::STATUS_REVOKED => 'Revoked',
            Mage_Webhook_Model_Subscription::STATUS_INACTIVE => 'Inactive',
        );
        $this->assertEquals($expectedArray, $object->toOptionArray());
    }

    /**
     * Translates array of inputs into string
     *
     * @param array $inputs
     * @return string
     */
    public static function translateCallback(array $inputs)
    {
        return implode("\n", $inputs);
    }
}