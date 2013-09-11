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
        $translatorMock = $this->getMockBuilder('Magento\Core\Model\Translate')
            ->disableOriginalConstructor()
            ->getMock();
        $translatorMock->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translateCallback')));
        $object = new \Magento\Webhook\Model\Subscription\Options\Status($translatorMock);
        $expectedArray = array(
            \Magento\Webhook\Model\Subscription::STATUS_ACTIVE => 'Active',
            \Magento\Webhook\Model\Subscription::STATUS_REVOKED => 'Revoked',
            \Magento\Webhook\Model\Subscription::STATUS_INACTIVE => 'Inactive',
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
