<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Magento\Store\Test\Fixture\Store;
use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;

/**
 * Class AssertStoreInGrid
 * Assert that created Store View can be found in Stores grid
 */
class AssertStoreInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Store View can be found in Stores grid by name
     *
     * @param StoreIndex $storeIndex
     * @param Store $store
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, Store $store)
    {
        $storeName = $store->getName();
        $storeIndex->open()->getStoreGrid()->search(['store_title' => $storeName]);
        \PHPUnit_Framework_Assert::assertTrue(
            $storeIndex->getStoreGrid()->isStoreExists($storeName),
            'Store \'' . $storeName . '\' is not present in grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Store View is present in grid.';
    }
}
