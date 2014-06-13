<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\CmsIndex as FrontCmsIndex;
use Magento\Cms\Test\Page\CmsPage as FrontCmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCmsPagePreview
 * Assert that content of created cms page displayed in section 'maincontent' and equals passed from fixture.
 */
class AssertCmsPagePreview extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that content of created cms page displayed in section 'maincontent' and equals passed from fixture.
     *
     * @param CmsIndex $cmsIndex
     * @param FrontCmsIndex $frontCmsIndex
     * @param FrontCmsPage $frontCmsPage
     * @param CmsPage $cms
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        FrontCmsIndex $frontCmsIndex,
        FrontCmsPage $frontCmsPage,
        CmsPage $cms,
        Browser $browser
    ) {
        $cmsIndex->open();
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $browser->selectWindow();
        \PHPUnit_Framework_Assert::assertEquals(
            $cms->getTitle(),
            $frontCmsIndex->getTitleBlock()->getTitle(),
            'Wrong title is displayed.'
        );
        if ($cms->getContent()) {
            \PHPUnit_Framework_Assert::assertEquals(
                $cms->getTitle(),
                $frontCmsPage->getCmsPageBlock()->getPageContent(),
                'Wrong content is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page displayed equals data from fixture.';
    }
}
