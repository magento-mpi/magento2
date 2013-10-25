<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Multishipping;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Addresses
 * Multishipping checkout choose item addresses block
 *
 * @package Magento\Checkout\Test\Block\Multishipping
 */
class Addresses extends Block
{
    /**
     * 'Enter New Address' button
     *
     * @var string
     */
    private $newAddress;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->newAddress = '[data-role="add-new-address"]';
    }

    /**
     * Add new customer address
     */
    public function addNewAddress()
    {
        $this->_rootElement->find($this->newAddress, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Select shipping addresses for products
     *
     * @param Checkout $fixture
     */
    public function selectAddresses(Checkout $fixture)
    {
        $products = $fixture->getBindings();
        foreach ($products as $key => $value) {
            $this->_rootElement->find('//tr[//a[text()="' . $key . '"]]/following-sibling::*//select',
                Locator::SELECTOR_XPATH, 'select')->setValue($value);
        }
        $btnLocation = '//button[@class="action continue" and @data-role="can-continue"]';
        $this->_rootElement->find($btnLocation, Locator::SELECTOR_XPATH)->click();
    }
}
