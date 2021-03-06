<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * Add by SKU form selector.
     *
     * @var string
     */
    protected $addBySkuForm = '.form-addbysku';

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
        $browser = $this->browser;
        $addBySkuForm = $this->addBySkuForm;
        $browser->waitUntil(
            function () use ($browser, $addBySkuForm) {
                $element = $browser->find($addBySkuForm);
                return $element->isVisible() ? true : null;
            }
        );
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
