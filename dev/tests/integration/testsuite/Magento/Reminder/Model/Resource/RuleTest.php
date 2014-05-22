<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Resource\Rule;

use Magento\Reminder\Model\Rule;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Reminder/_files/customersForNotification.php
     * @dataProvider dataProviderTestGetCustomersForNotification
     */
    public function testGetCustomersForNotification($customersForNotification)
    {
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reminder\Model\Resource\Rule\Collection'
        );
        $rules = $collection->addIsActiveFilter(1);
        $ruleResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reminder\Model\Resource\Rule'
        );
        foreach ($rules as $rule) {
            $customersForNotification[0]['rule_id'] = $rule->getId();
            $salesRule = null;
            $websiteIds = $rule->getWebsiteIds();
            foreach ($websiteIds as $websiteId) {
                $ruleResource->saveMatchedCustomers($rule, $salesRule, $websiteId, null);
            }
        }
        $this->assertEquals($customersForNotification, $ruleResource->getCustomersForNotification());
    }

    public function dataProviderTestGetCustomersForNotification()
    {
        return ['first' =>
            [[['customer_id' => 1, 'coupon_id' => null, 'rule_id' => null, 'schedule' => null,
                'log_sent_at_max' => null, 'log_sent_at_min' => null]]]
        ];
    }
}
