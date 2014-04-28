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
 * Class AssertDownloadableSamplesData
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertDownloadableSamplesData extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert Sample block for downloadable product on front-end
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
            $catalogProductView->getViewBlock()->downloadSamplesData($product),
            'Sample block for downloadable product on front-end is not visible.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Sample block for downloadable product on front-end is visible.';
    }
}
