<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Store\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\StoreIndex;
use Magento\Backend\Test\Page\Adminhtml\StoreNew;
use Magento\Store\Test\Fixture\StoreGroup;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertStoreGroupOnStoreViewForm
 * Assert that New Store Group visible on StoreView Form in Store dropdown
 */
class AssertStoreGroupOnStoreViewForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that New Store Group visible on StoreView Form in Store dropdown
     *
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, StoreNew $storeNew, StoreGroup $storeGroup)
    {
        $storeGroupName = $storeGroup->getName();
        $storeIndex->open()->getGridPageActions()->addStoreView();
        \PHPUnit_Framework_Assert::assertTrue(
            $storeNew->getStoreForm()->isStoreVisible($storeGroupName),
            'Store Group \'' . $storeGroupName . '\' is not present on StoreView Form in Store dropdown.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store Group is visible on StoreView Form in Store dropdown.';
    }
}
