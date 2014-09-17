<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block\Customer;

use Mtf\Block\Form;

/**
 * Class Sku
 * Order by SKU form
 */
class Sku extends Form
{
    /**
     * Add to Cart button selector
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * Click Add to Cart button
     *
     * @return void
     */
    public function addToCart()
    {
        $this->_rootElement->find($this->addToCart)->click();
    }

    /**
     * Fill order by SKU form
     *
     * @param array $orderOptions
     * @return void
     */
    public function fillForm(array $orderOptions)
    {
        $mapping = $this->dataMapping($orderOptions);
        $this->_fill($mapping);
    }
}
