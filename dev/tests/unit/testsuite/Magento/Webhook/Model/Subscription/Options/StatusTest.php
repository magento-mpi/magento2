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
        $translatorMock = $this->getMockBuilder('Magento_Core_Model_Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $translatorMock->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translateCallback')));
        $object = new Magento_Webhook_Model_Subscription_Options_Status($translatorMock);
        $expectedArray = array(
            Magento_Webhook_Model_Subscription::STATUS_ACTIVE => 'Active',
            Magento_Webhook_Model_Subscription::STATUS_REVOKED => 'Revoked',
            Magento_Webhook_Model_Subscription::STATUS_INACTIVE => 'Inactive',
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
