<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Constraint;

use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\CmsPage as Page;

/**
 * Class AssertCmsPagePreview
 * Assert that content of created cms page displayed in section 'maincontent' and equals passed from fixture
 */
class AssertCmsPagePreview extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that content of created cms page displayed in section 'maincontent' and equals passed from fixture
     *
     * @param CmsPage $cms
     * @param CmsIndex $cmsIndex
     * @param Page $cmsPage
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsIndex $cmsIndex,
        Page $cmsPage,
        Browser $browser
    ) {
        $filter = ['title' => $cms->getTitle()];
        $cmsIndex->open();
        $cmsIndex->getCmsPageGridBlock()->searchAndPreview($filter);
        $browser->selectWindow();
        $pageContent = $cmsPage->getCmsPageBlock()->getPageContent();
        $fixtureContent = $cms->getContent()['content'];

        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureContent,
            $pageContent,
            'Page content is not equals to expected'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Page content is equal to expected.';
    }
}
