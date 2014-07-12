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
        $cmsIndex->getCmsPageGridBlock()->searchAndPreview($filter);
        $browser->selectWindow();

        $fixtureContent = $cms->getContent();
        \PHPUnit_Framework_Assert::assertContains(
            $fixtureContent['content'],
            $frontendCmsPageContent = $frontCmsPage->getCmsPageBlock()->getPageContent(),
            'Wrong content is displayed.'
        );
        if (isset($fixtureContent['widget'])) {
            $widgetSelectors = $this->getWidgetsSelectors($fixtureContent['widget']);
            foreach($widgetSelectors as $widgetType => $widgetSelector){
                \PHPUnit_Framework_Assert::assertTrue(
                    $frontCmsPage->widgetSelectorIsVisible($widgetSelector),
                    'Widget \'' . $widgetType . '\' is not displayed.'
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
     * Get widgets selectors
     *
     * @param $contentWidgets
     * @return array
     */
    protected function getWidgetsSelectors($contentWidgets)
    {
        $widgetSelectors = [];
        foreach ($contentWidgets['preset'] as $widget) {
            switch ($widget['widget_type']) {
                case 'CMS Page Link':
                    $widgetSelectors[$widget['widget_type']] = '.widget.widget-cms-link';
                    break;
                case 'Catalog Category Link':
                    $widgetSelectors[$widget['widget_type']] = '.widget.category.link';
                    break;
                case 'Catalog Product Link':
                    $widgetSelectors[$widget['widget_type']] = '.widget.product.link';
                    break;
                case 'Recently Compared Products':
                    $widgetSelectors[$widget['widget_type']] = '.block.compare';
                    break;
                case 'Recently Viewed Products':
                    $widgetSelectors[$widget['widget_type']] = '.block.viewed.links';
                    break;
            }
        }

        return $widgetSelectors;
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
