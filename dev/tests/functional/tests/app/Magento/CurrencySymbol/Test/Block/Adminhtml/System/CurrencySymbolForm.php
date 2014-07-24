<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CurrencySymbolForm
 * Currency Symbol form
 */
class CurrencySymbolForm extends Form
{

    /**
     * Custom Currency locator
     *
     * @var string
     */
    protected $currencyRow = '//tr[td/label[@for="custom_currency_symbol%s"]]';

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $currencyCode = explode(" ", $fixture->getCode());
        $element = $this->_rootElement->find(sprintf($this->currencyRow, $currencyCode[0]), Locator::SELECTOR_XPATH);
        return parent::fill($fixture, $element);
    }
}
