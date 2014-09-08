<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Order totals block
 *
 */
class History extends Block
{
    /**
     * Comment history Id
     *
     * @var string
     */
    protected $commentHistory = '.note-list-comment';

    /**
     * Captured Amount from IPN
     *
     * @var string
     */
    protected $capturedAmount = '//div[@class="note-list-comment"][contains(text(), "captured amount of")]';

    /**
     * Get comments history
     *
     * @return string
     */
    public function getCommentsHistory()
    {
        return $this->_rootElement->find($this->commentHistory, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get the captured amount from the comments history
     *
     * @return string
     */
    public function getCapturedAmount()
    {
        $element = $this->_rootElement;
        $selector = $this->capturedAmount;
        $this->_rootElement->waitUntil(
            function () use ($element, $selector) {
                $addProductsButton = $element->find($selector, Locator::SELECTOR_XPATH);
                return $addProductsButton->isVisible() ? true : null;
            }
        );
        return $this->_rootElement->find($this->capturedAmount, Locator::SELECTOR_XPATH)->getText();
    }
}
