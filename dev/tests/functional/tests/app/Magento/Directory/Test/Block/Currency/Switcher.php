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
 * Class Switcher
 * Switcher Currency Symbol
 */
class Switcher extends Block
{
    /**
     * Currency switch locator
     *
     * @var string
     */
    protected $currencySwitch = '#currency-switcher';

    /**
     * Currency link locator
     *
     * @var string
     */
    protected $currencyLinkLocator = '//li[@class="currency-%s"]//a';

    /**
     * Switch currency to specified one
     *
     * @param string $currencyCode
     * @return void
     */
    public function switchCurrency($currencyCode)
    {
        $currencyLink = $this->_rootElement->find($this->currencySwitch);
        $customCurrencySwitch = explode(" ", $this->getCurrentCurrencyCode());
        $currencyCode = explode(" ", $currencyCode);
        if ($customCurrencySwitch[0] !== $currencyCode[0]) {
            $currencyLink->click();
            $currencyLink = $this->_rootElement
                ->find(sprintf($this->currencyLinkLocator, $currencyCode[0]), Locator::SELECTOR_XPATH);
            $currencyLink->click();
        }
    }

    /**
     * Get Currency from Cms page
     *
     * @return string
     */
    protected function getCurrentCurrencyCode()
    {
        return $this->_rootElement->find($this->currencySwitch)->getText();
    }
}
