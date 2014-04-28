<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertDownloadableLinks
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertDownloadableLinks extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Text value for checking Stock Availability
     */
    const STOCK_AVAILABILITY = 'In stock';

    /**
     * Assert Link block for downloadable product on front-end
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $catalogProductView->init($product);
        $catalogProductView->open();
        \PHPUnit_Framework_Assert::assertTrue(
            true,
            $catalogProductView->getViewBlock()->downloadLinksData($product),
            'Link block for downloadable product on front-end is not visible.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Link block for downloadable product on front-end is visible.';
    }
}
