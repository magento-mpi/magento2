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

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Order totals block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
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
     * Comment history Id using PayPal Standard payment method
     *
     * @var string
     */
    protected $paypalStandardCommentHistory = "//li[2]/div[@class='note-list-comment']";

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
     * Get comments history when using PayPal Standard payment method
     *
     * @return string
     */
    public function getPaypalStandardCommentsHistory()
    {
        return $this->_rootElement->find($this->paypalStandardCommentHistory, Locator::SELECTOR_XPATH)->getText();
    }
}
