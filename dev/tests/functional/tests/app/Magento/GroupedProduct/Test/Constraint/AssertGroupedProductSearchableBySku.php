<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Constraint\AssertProductSearchableBySku;

/**
 * Class AssertGroupedProductSearchableBySku
 */
class AssertGroupedProductSearchableBySku extends AssertProductSearchableBySku
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Search product on frontend
     *
     * @param CmsIndex $cmsIndex
     * @param FixtureInterface $product
     * @return void
     */
    protected function searchBy(CmsIndex $cmsIndex, FixtureInterface $product)
    {
        $cmsIndex->getSearchBlock()->search($product->getName());
    }
}
