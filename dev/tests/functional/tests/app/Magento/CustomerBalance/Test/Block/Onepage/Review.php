<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Onepage;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Review
 * One page checkout order review block
 */
class Review extends \Magento\Checkout\Test\Block\Onepage\Review
{
    /**
     * Remove store credit
     *
     * @var string
     */
    protected $removeButton = '//td[contains(.,"Store Credit")]/a';

    /**
     * Click on 'Remove Store Credit' link
     *
     * @return void
     */
    public function clickRemoveStoreCredit()
    {
        $this->_rootElement->find($this->removeButton, Locator::SELECTOR_XPATH)->click();
    }
}
