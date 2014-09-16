<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Test\TestCase;

use Magento\Backend\Test\Page\Adminhtml\Dashboard;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Fixture\InjectableFixture;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for GlobalSearchEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create two simple products
 * 3. Create order with one of created simple product
 *
 * Steps:
 * 1. Login to backend
 * 2. Click on Search button on the top of page
 * 3. Fill in data according dataSet
 * 4. Perform assertions
 *
 * @group Search_Core_(MX)
 * @ZephyrId MAGETWO-28457
 */
class GlobalSearchEntityTest extends Injectable
{
    /**
     * Backend Dashboard page
     *
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * Preparing data for test
     *
     * @param OrderInjectable $order
     * @return array
     */
    public function __prepare(OrderInjectable $order)
    {
        $order->persist();
        return ['order' => $order];
    }

    /**
     * Preparing pages for test
     *
     * @param Dashboard $dashboard
     * @return void
     */
    public function __inject(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * Run Global Search Entity Test
     *
     * @param OrderInjectable $order
     * @param array $query
     * @param string $searchByTwoLetters
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function test(OrderInjectable $order, array $query, $searchByTwoLetters)
    {
        /** @var \Magento\Customer\Test\Fixture\CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();
        /** @var InjectableFixture $product */
        $product = $order->getDataFieldConfig('entity_id')['source']->getData()['products'][0];
        $query = array_filter($query);
        $entity = key($query);
        $textToSearch = $$entity->getData($query[$entity]);
        $textToSearch = $searchByTwoLetters ? $searchByTwoLetters : $textToSearch;

        //Steps:
        $this->dashboard->open();
        $this->dashboard->getAdminPanelHeader()->search($textToSearch);
    }
}
