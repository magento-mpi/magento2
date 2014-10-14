<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Composite;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Block\AbstractConfigureBlock;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class Configure
 * Adminhtml catalog product composite configure block
 */
class Configure extends AbstractConfigureBlock
{
    /**
     * Custom options CSS selector
     *
     * @var string
     */
    protected $customOptionsSelector = '#product_composite_configure_fields_options';

    /**
     * Selector for "Ok" button
     *
     * @var string
     */
    protected $okButton = '.ui-dialog-buttonset button:nth-of-type(2)';

    /**
     * Set quantity
     *
     * @param int $qty
     * @return void
     */
    public function setQty($qty)
    {
        $this->_fill($this->dataMapping(['qty' => $qty]));
    }

    /**
     * Fill in the option specified for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function configProduct(FixtureInterface $product)
    {
        $checkoutData = null;
        if ($product instanceof InjectableFixture) {
            /** @var CatalogProductSimple $product */
            $checkoutData = $product->getCheckoutData();
        }

        $this->fillOptions($product);
        if (isset($checkoutData['qty'])) {
            $this->setQty($checkoutData['qty']);
        }
        $this->clickOk();
    }

    /**
     * Click "Ok" button
     *
     * @return void
     */
    protected function clickOk()
    {
        $this->_rootElement->find($this->okButton)->click();
    }
}
