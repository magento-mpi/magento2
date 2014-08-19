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
use Magento\Backend\Test\Page\Adminhtml\StoreNew;
use Magento\Store\Test\Fixture\StoreGroup;

/**
 * Class AssertStoreOnStoreViewForm
 * Assert that New Store visible on StoreView Form in Store dropdown
 */
class AssertStoreOnStoreViewForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that New Store visible on StoreView Form in Store dropdown
     *
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, StoreNew $storeNew, StoreGroup $storeGroup)
    {
        $storeName = $storeGroup->getName();
        $storeIndex->open()->getGridPageActions()->addStoreView();
        \PHPUnit_Framework_Assert::assertTrue(
            $storeNew->getStoreForm()->isStoreVisible($storeName),
            'Store \'' . $storeName . '\' is not present on StoreView Form in Store dropdown.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store visible on StoreView Form in Store dropdown.';
    }
}
