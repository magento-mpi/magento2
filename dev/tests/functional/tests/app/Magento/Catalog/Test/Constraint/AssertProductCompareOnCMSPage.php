<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductCompareOnCMSPage
 */
class AssertProductCompareOnCMSPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Compare Products block is presented on CMS pages e.g. "About Us".
     * Block contains information about compared products
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        $cmsIndex->getFooterBlock()->clickLink('About Us');
        $content = $cmsIndex->getCompareProductsBlock()->getContent();
        \PHPUnit_Framework_Assert::assertFalse(
            $content == 'You have no items to compare.',
            $content
        );
        // TODO after fix bug MAGETWO-22756 add next steps
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Block contains information about compared products.';
    }
}
