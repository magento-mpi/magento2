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
use Magento\Store\Test\Fixture\Website;

/**
 * Class AssertWebsiteNotInGrid
 * Assert that deleted website is absent in grid
 */
class AssertWebsiteNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created Website can't be found in grid by name
     *
     * @param StoreIndex $storeIndex
     * @param Website $website
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, Website $website)
    {
        $websiteName = $website->getName();
        $storeIndex->open()->getStoreGrid()->search(['website_title' => $websiteName]);
        \PHPUnit_Framework_Assert::assertFalse(
            $storeIndex->getStoreGrid()->isWebsiteExists($website),
            'Website \'' . $websiteName . '\' is present in grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Website is absent in grid.';
    }
}
