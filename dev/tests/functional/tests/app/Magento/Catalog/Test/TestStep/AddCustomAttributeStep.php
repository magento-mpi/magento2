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

/**
 * Add custom attribute to product.
 */
class AddCustomAttributeStep implements TestStepInterface
{

    /**
     * Catalog product index page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * @constructor
     * @param CatalogProductEdit $catalogProductEdit
     */
    public function __construct(CatalogProductEdit $catalogProductEdit)
    {
        $this->catalogProductEdit = $catalogProductEdit;
    }

    /**
     * Add custom attribute to product.
     *
     * @return void
     */
    public function run()
    {
        $productForm = $this->catalogProductEdit->getProductForm();
        /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\ProductTab $productDetailsTab */
        $productDetailsTab = $productForm->getTabElement('product-details');
        $productDetailsTab->addNewAttribute();
    }
}
