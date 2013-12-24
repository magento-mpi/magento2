<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Test\Block\Currency;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * @package Magento\Directory\Test\Block\Currency
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
        $categoryLink = $this->_rootElement->find(
            "//div[contains(@class, 'switcher')][contains(@class, 'currency')]"
            . "//button[contains(@class, 'action')][contains(@class, 'switch')]",
            Locator::SELECTOR_XPATH
        );
        $categoryLink->click();

        $categoryLink = $this->_rootElement->find(
            '//li[@class="currency-' . $currencyCode . '"]//a',
            Locator::SELECTOR_XPATH
        );
        $categoryLink->click();
    }
}
