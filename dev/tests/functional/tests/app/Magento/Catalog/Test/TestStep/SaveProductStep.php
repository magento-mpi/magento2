<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestStep;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mtf\TestStep\TestStepInterface;
use Mtf\Fixture\InjectableFixture;

/**
 * Save product step.
 */
class SaveProductStep implements TestStepInterface
{
    /**
     * Product fixture.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Catalog product edit page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * @constructor
     * @param InjectableFixture $product
     * @param CatalogProductEdit $catalogProductEdit
     */
    public function __construct(InjectableFixture $product, CatalogProductEdit $catalogProductEdit)
    {
        $this->product = $product;
        $this->catalogProductEdit = $catalogProductEdit;
    }

    /**
     * Save product.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductEdit->getFormPageActions()->save($this->product);
    }
}
