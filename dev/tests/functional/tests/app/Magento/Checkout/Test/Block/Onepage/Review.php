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

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * One page checkout status
 *
 * @package Magento\Checkout\Test\Block\Onepage
 */
class Review extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->continue = '#review-buttons-container button';
    }

    /**
     * Fill billing address
     */
    public function placeOrder()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('#review-please-wait');
    }
}
