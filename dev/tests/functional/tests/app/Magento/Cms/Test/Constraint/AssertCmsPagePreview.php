<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;
use Magento\Cms\Test\Page\CmsIndex as FrontCmsIndex;
use Magento\Cms\Test\Page\CmsPage as FrontCmsPage;
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
        $cmsIndex->getCmsPageGridBlock()->searchAndPreview($filter);
        $browser->selectWindow();

        $fixtureContent = $cms->getContent();
        \PHPUnit_Framework_Assert::assertContains(
            $fixtureContent['content'],
            $frontCmsPage->getCmsPageBlock()->getPageContent(),
            'Wrong content is displayed.'
        );
        if (isset($fixtureContent['widget'])) {
            foreach ($fixtureContent['widget']['preset'] as $widget) {
                \PHPUnit_Framework_Assert::assertTrue(
                    $frontCmsPage->getCmsPageBlock()->isWidgetVisible($widget['widget_type']),
                    'Widget \'' . $widget['widget_type'] . '\' is not displayed.'
                );
            }
        }
        if ($cms->getContentHeading()) {
            \PHPUnit_Framework_Assert::assertEquals(
                $cms->getContentHeading(),
                $frontCmsIndex->getTitleBlock()->getTitle(),
                'Wrong title is displayed.'
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
        return 'CMS Page content equals to data from fixture.';
    }
}
