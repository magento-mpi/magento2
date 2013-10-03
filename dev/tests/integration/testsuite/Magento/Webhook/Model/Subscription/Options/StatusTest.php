<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Subscription\Options;

/**
 * \Magento\Webhook\Model\Subscription\Options\Status
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $translator = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
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
