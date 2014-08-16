<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Magento\Catalog\Test\Constraint\AssertProductCustomOptionsOnProductPage;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductCustomOptionsOnBundleProductPage
 * Assertion that commodity options are displayed correctly on bundle product page
 */
class AssertProductCustomOptionsOnBundleProductPage extends AssertProductCustomOptionsOnProductPage
{
    /**
     * Flag for verify price data
     *
     * @var bool
     */
    protected $isPrice = false;

    /**
     * Class name of catalog product view page
     *
     * @var string
     */
    protected $catalogProductViewClass = 'Magento\Bundle\Test\Page\Product\CatalogProductView';

    /**
     * Open product view page
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function openProductPage(FixtureInterface $product)
    {
        $this->catalogProductView->init($product);
        $this->catalogProductView->open();
        $this->catalogProductView->getViewBlock()->clickCustomize();
    }
}
