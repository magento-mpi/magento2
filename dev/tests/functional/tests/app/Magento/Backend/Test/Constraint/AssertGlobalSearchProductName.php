<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Backend\Test\Constraint;

use Magento\Backend\Test\Fixture\GlobalSearch;
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
     * @param GlobalSearch $search
     * @return void
     */
    public function processAssert(Dashboard $dashboard, GlobalSearch $search)
    {
        $entity = $search->getDataFieldConfig('query')['source']->getEntity();
        $product = $entity instanceof OrderInjectable
            ? $entity->getEntityId()['products'][0]
            : $entity;
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
