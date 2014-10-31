<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Mtf\TestStep\TestStepInterface;
use Mtf\Fixture\FixtureInterface;

/**
 * Open product on backend.
 */
class OpenProductOnBackendStep implements TestStepInterface
{
    /**
     * Product fixture.
     *
     * @var FixtureInterface
     */
    protected $product;

    /**
     * Catalog product index page.
     *
     * @var CatalogProductIndex
     */
    protected $catalogProductIndex;

    /**
     * @constructor
     * @param FixtureInterface $product
     * @param CatalogProductIndex $catalogProductIndex
     */
    public function __construct(FixtureInterface $product, CatalogProductIndex $catalogProductIndex)
    {
        $this->product = $product;
        $this->catalogProductIndex = $catalogProductIndex;
    }

    /**
     * Open products on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $this->product->getSku()]);
    }
}
