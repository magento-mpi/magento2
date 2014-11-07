<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Resource;

use Magento\Reminder\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Reminder/_files/customersForNotification.php
     * @ magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testGetCustomersForNotification()
    {
        $beforeYesterday = date('Y-m-d 03:00:00', strtotime('-2 day', time()));
        $customersForNotification = [['customer_id' => '1', 'coupon_id' => null, 'rule_id' => null, 'schedule' => '2',
                'log_sent_at_max' => $beforeYesterday, 'log_sent_at_min' => $beforeYesterday]];
        /** @var \Magento\Framework\App\Resource $resource */
        $resource = Bootstrap::getObjectManager()->get('Magento\Framework\App\Resource');
        $adapter = $resource->getConnection('core_write');
        $adapter->query("UPDATE {$resource->getTableName('sales_quote')} SET updated_at = '{$beforeYesterday}'");

        $collection = Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Resource\Rule\Collection');
        $rules = $collection->addIsActiveFilter(1);
        $ruleResource = Bootstrap::getObjectManager()->create('Magento\Reminder\Model\Resource\Rule');
        foreach ($rules as $rule) {
            $customersForNotification[0]['rule_id'] = $rule->getId();
            $adapter->query("INSERT INTO {$resource->getTableName('magento_reminder_rule_log')} " .
                "(`rule_id`, `customer_id`, `sent_at`) VALUES ({$rule->getId()}, 1, '{$beforeYesterday}');");
            $websiteIds = $rule->getWebsiteIds();
            foreach ($websiteIds as $websiteId) {
                $ruleResource->saveMatchedCustomers($rule, null, $websiteId, null);
            }
        }
        $this->assertEquals($customersForNotification, $ruleResource->getCustomersForNotification());
    }
}
