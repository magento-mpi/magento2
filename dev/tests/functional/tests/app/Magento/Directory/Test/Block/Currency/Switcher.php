<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Test\Block\Currency;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 */
class Switcher extends Block
{
    /**
     * Switch currency on specified one
     *
     * @param string $currencyCode
     * @return bool
     */
    public function switchCurrency($currencyCode)
    {
        $categoryLink = $this->_rootElement->find('#currency-switcher');

        $customCurrencySwitch = explode(" ", $this->getCurrency());
        if ($customCurrencySwitch[0] == $currencyCode) {
            return true;
        }

        $categoryLink->click();
        $categoryLink = $this->_rootElement->find(
            '//li[@class="currency-' . $currencyCode . '"]//a',
            Locator::SELECTOR_XPATH
        );
        $categoryLink->click();
    }

    /**
     * Get Currency from Cms page
     *
     * @return string
     */
    protected function getCurrency()
    {
        return $this->_rootElement->find('#currency-switcher')->getText();
    }
}
