<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Store\Test\Fixture\StoreGroup;

/**
 * Class AssertStoreGroupInGrid
 * Assert that created Store Group can be found in Stores grid
 */
class AssertStoreGroupInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created Store Group can be found in Stores grid by name
     *
     * @param StoreIndex $storeIndex
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, StoreGroup $storeGroup)
    {
        $storeGroupName = $storeGroup->getName();
        $storeIndex->open()->getStoreGrid()->search(['group_title' => $storeGroupName]);
        \PHPUnit_Framework_Assert::assertTrue(
            $storeIndex->getStoreGrid()->isStoreExists($storeGroupName),
            'Store group \'' . $storeGroupName . '\' is not present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store Group is present in grid.';
    }
}
