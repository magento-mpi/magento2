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

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * Paypal sandbox review block
 *
 * @package Magento\Paypal\Test\Block
 */
class Review extends Block
{
    /**
     * Continue button
     *
     * @var string
     */
    private $continue;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->continue = '#continue';
    }

    /**
     * Press 'Continue' button
     */
    public function continueCheckout()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
    }
}
