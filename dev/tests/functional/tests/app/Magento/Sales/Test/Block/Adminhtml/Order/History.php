<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * Note list locator
     *
     * @var string
     */
    protected $noteList = '.note-list';

    /**
     * Get comments history
     *
     * @return string
     */
    public function getCommentsHistory()
    {
        $this->waitCommentsHistory();
        return $this->_rootElement->find($this->commentHistory, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get the captured amount from the comments history
     *
     * @return string
     */
    public function getCapturedAmount()
    {
        $this->waitCommentsHistory();
        return $this->_rootElement->find($this->capturedAmount, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Wait for comments history is visible
     *
     * @return void
     */
    protected function waitCommentsHistory()
    {
        $element = $this->_rootElement;
        $selector = $this->noteList;
        $element->waitUntil(
            function () use ($element, $selector) {
                return $element->find($selector)->isVisible() ? true : null;
            }
        );
    }
}
