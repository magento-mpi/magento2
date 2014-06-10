<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint; 

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCategoryNotExist
 * Assert that not displayed category in backend catalog category tree
 */
class AssertCategoryNotExist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that not displayed category in backend catalog category tree
     *
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @param CatalogCategory $category
     * @return void
     */
    public function processAssert(CatalogCategoryIndex $catalogCategoryIndex, CatalogCategory $category)
    {
        $catalogCategoryIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogCategoryIndex->getTreeCategories()->findCategory($category->getPath() . '/' . $category->getName()),
            'Category is displayed in backend catalog category tree.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Category not displayed in backend catalog category tree.';
    }
}
