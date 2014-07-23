<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System;

use  Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

class CurrencySymbolForm extends Form
{
    /**
     * @var
     */
    protected $currency;

    /**
     * Custom Currency locator
     *
     * @var string
     */
    protected $customCurrencyLocator = './/*/tbody//*/label[substring-before(text()," ") = "%s"]';

    /**
     * Init Currency
     *
     * @param string $customCurrency
     * @return void
     */
    public function initCurrency($customCurrency)
    {
        $this->currency = $customCurrency;
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */

    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $data = $fixture->getData();
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        foreach ($mapping as $key => $field) {
            $mapping[$key]['selector'] = $field['selector'] . $this->currency;
        }
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Find Currency in form
     *
     * @param string $customCurrency
     * @return string
     * @throws \Exception
     */
    public function getCurrency($customCurrency)
    {
        $locator = sprintf($this->customCurrencyLocator, $customCurrency);
        if ($this->_rootElement->find($locator, Locator::SELECTOR_XPATH)->isVisible()
        ) {
            return $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)->getText();
        } else {
            throw new \Exception('Currency \'' . $customCurrency . '\' is absent');
        }
    }
}

