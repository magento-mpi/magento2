<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * Paypal sandbox review block
 */
class Review extends Block
{
    /**
     * Continue button
     *
     * @var string
     */
    protected $continue = 'input[type="submit"]';

    /**
     * Press 'Continue' button
     */
    public function continueCheckout()
    {
        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
    }
}
