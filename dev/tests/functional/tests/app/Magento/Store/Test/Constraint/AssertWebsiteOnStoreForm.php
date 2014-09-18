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
use Magento\Backend\Test\Page\Adminhtml\NewGroupIndex;
use Magento\Store\Test\Fixture\Website;

/**
 * Class AssertWebsiteOnStoreForm
 * Assert that Website visible on Store Group Form in Website dropdown
 */
class AssertWebsiteOnStoreForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Website visible on Store Group Form in Website dropdown
     *
     * @param StoreIndex $storeIndex
     * @param NewGroupIndex $newGroupIndex
     * @param Website $website
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, NewGroupIndex $newGroupIndex, Website $website)
    {
        $websiteName = $website->getName();
        $storeIndex->open()->getGridPageActions()->createStoreGroup();
        \PHPUnit_Framework_Assert::assertTrue(
            $newGroupIndex->getEditFormGroup()->isWebsiteVisible($websiteName),
            'Website \'' . $websiteName . '\' is not present on Store Group Form in Website dropdown.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Website is visible on Store Group Form in Website dropdown.';
    }
}
