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
use Mtf\Fixture\FixtureInterface;

/**
 * Save product step.
 */
class SaveProductStep implements TestStepInterface
{
    /**
     * Product fixture.
     *
     * @var FixtureInterface
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
     * @param FixtureInterface $product
     * @param CatalogProductEdit $catalogProductEdit
     */
    public function __construct(FixtureInterface $product, CatalogProductEdit $catalogProductEdit)
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
