<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertCmsBlockOnCategoryPage
 * Assert that created CMS block displayed on frontend category page
 */
class AssertCmsBlockOnCategoryPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that created CMS block displayed on frontend category page (in order to assign block to category:
     * go to category page> Display settings> CMS Block)
     *
     * @param CmsIndex $cmsIndex
     * @param CmsBlock $cmsBlock
     * @param CatalogCategoryView $catalogCategoryView
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CmsBlock $cmsBlock,
        CatalogCategoryView $catalogCategoryView,
        FixtureFactory $fixtureFactory
    ) {
        $category = $fixtureFactory->createByCode(
            'catalogCategory',
            [
                'dataSet' => 'default_subcategory',
                'data' => [
                    'display_mode' => 'Static block and products',
                    'landing_page' => $cmsBlock->getTitle(),
                ]
            ]
        );
        $category->persist();

        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getName());
        $categoryViewContent = $catalogCategoryView->getViewBlock()->getContent();

        \PHPUnit_Framework_Assert::assertEquals(
            $cmsBlock->getContent(),
            $categoryViewContent,
            'Wrong block content on category is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS block description is present on Category page (frontend).';
    }
}
