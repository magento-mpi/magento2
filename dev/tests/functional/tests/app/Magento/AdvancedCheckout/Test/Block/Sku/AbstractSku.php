<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block\Sku;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Order By SKU form.
 */
abstract class AbstractSku extends Form
{
    /**
     * Add to Cart button selector
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * Add new row button selector
     *
     * @var string
     */
    protected $addRow = '[id^="add_new_item_button"]';

    /**
     * Row selector
     *
     * @var string
     */
    protected $row = '//*[contains(@class,"fields additional") and .//*[contains(@id,"id-items[%d_")]]';

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
        foreach ($orderOptions as $key => $value) {
            if ($key !== 0) {
                $this->_rootElement->find($this->addRow)->click();
            }
            $element = $this->_rootElement->find(sprintf($this->row, $key), Locator::SELECTOR_XPATH);
            $mapping = $this->dataMapping($value);
            $this->_fill($mapping, $element);
        }
    }
}
