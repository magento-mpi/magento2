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
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class AssertCategoryIsNotActive
 * Assert that the category cannot be accessed from the navigation bar in the frontend
 */
class AssertCategoryIsNotActive extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that the category cannot be accessed from the navigation bar in the frontend
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CatalogCategory $category)
    {
        $cmsIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $cmsIndex->getTopmenu()->isVisibleCategory($category->getName()),
            'Category can be accessed from the navigation bar in the frontend.'
        );
    }

    /**
     * Category not find in top menu
     *
     * @return string
     */
    public function toString()
    {
        return 'Category cannot be accessed from the navigation bar.';
    }
}
