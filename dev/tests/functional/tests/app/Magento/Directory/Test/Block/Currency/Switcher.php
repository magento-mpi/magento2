<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Test\Block\Currency;

use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
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
    protected $currencySwitch = '#switcher-currency-trigger';

    /**
     * Currency link locator
     *
     * @var string
     */
    protected $currencyLinkLocator = '//li[@class="currency-%s switcher-option"]//a';

    /**
     * Switch currency to specified one
     *
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function switchCurrency(CurrencySymbolEntity $currencySymbol)
    {

        $currencyLink = $this->_rootElement->find($this->currencySwitch);
        $customCurrencySwitch = explode(" ", $this->_rootElement->find($this->currencySwitch)->getText());
        $currencyCode = $currencySymbol->getCode();
        if ($customCurrencySwitch[0] !== $currencyCode) {
            $currencyLink->click();
            $currencyLink = $this->_rootElement
                ->find(sprintf($this->currencyLinkLocator, $currencyCode), Locator::SELECTOR_XPATH);
            $currencyLink->click();
        }
    }
}
