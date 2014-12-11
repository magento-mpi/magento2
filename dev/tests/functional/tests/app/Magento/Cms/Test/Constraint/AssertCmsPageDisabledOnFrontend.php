<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\CmsIndex as FrontCmsIndex;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPageDisabledOnFrontend
 * Assert that created CMS page with 'Status' - Disabled displays with '404 Not Found' message on Frontend.
 */
class AssertCmsPageDisabledOnFrontend extends AbstractConstraint
{
    const NOT_FOUND_MESSAGE = 'Whoops, our bad...';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS page with 'Status' - Disabled displays with '404 Not Found' message on Frontend.
     *
     * @param CmsPage $cms
     * @param FrontCmsIndex $frontCmsIndex
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CmsPage $cms, FrontCmsIndex $frontCmsIndex, CmsIndex $cmsIndex, Browser $browser)
    {
        $cmsIndex->open();
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->getCmsPageGridBlock()->searchAndPreview($filter);
        $browser->selectWindow();
        \PHPUnit_Framework_Assert::assertEquals(
            self::NOT_FOUND_MESSAGE,
            $frontCmsIndex->getTitleBlock()->getTitle(),
            'Wrong page is displayed.'
        );
    }

    /**
     * Not found page is display
     *
     * @return string
     */
    public function toString()
    {
        return 'Not found page is display.';
    }
}
