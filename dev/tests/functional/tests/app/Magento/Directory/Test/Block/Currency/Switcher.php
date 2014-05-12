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
     */
    public function switchCurrency($currencyCode)
    {
        $categoryLink = $this->_rootElement->find('#currency-switcher');

        $categoryLink->click();

        $categoryLink = $this->_rootElement->find(
            '//li[@class="currency-' . $currencyCode . '"]//a',
            Locator::SELECTOR_XPATH
        );
        $categoryLink->click();
    }
}
