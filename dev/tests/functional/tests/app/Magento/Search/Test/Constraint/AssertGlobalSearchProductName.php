<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\Dashboard;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGlobalSearchProductName
 * Assert that product name is present in search results
 */
class AssertGlobalSearchProductName extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product name is present in search results
     *
     * @param Dashboard $dashboard
     * @param OrderInjectable $order
     * @return void
     */
    public function processAssert(Dashboard $dashboard, OrderInjectable $order)
    {
        /** @var InjectableFixture $product */
        $product = $order->getDataFieldConfig('entity_id')['source']->getData()['products'][0];
        $productName = $product->getName();
        $isVisibleInResult = $dashboard->getAdminPanelHeader()->isSearchResultVisible($productName);
        \PHPUnit_Framework_Assert::assertTrue(
            $isVisibleInResult,
            'Product name ' . $productName . ' is absent in search results'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product name is present in search results';
    }
}
