<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * interface ConstrainPriceOnProductPageInterface
 * Interface for Constraints price on product page classes
 */
interface ConstrainPriceOnProductPageInterface
{
    /**
     * Verify product price on product view page
     *
     * @param FixtureInterface $product
     * @param CatalogProductView $catalogProductView
     * @param string $block
     * @return void
     */
    public function assertPrice(FixtureInterface $product, CatalogProductView $catalogProductView, $block);

    /**
     * Set $errorMessage for constraint
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage);
}
