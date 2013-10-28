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

namespace Magento\Backend\Test\Block\Sales\Order;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Totals
 * Order totals block
 *
 * @package Magento\Backend\Test\Block\Sales\Order
 */
class History extends Block
{
    /**
     * Comment history Id
     *
     * @var string
     */
    protected $_commentHistoryClass;

    /**
     * Authorized amount Id
     *
     * @var string
     */
    protected $_authorizedAmount;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_commentHistoryClass    = 'note-list-comment';
        $this->_authorizedAmount  = '#order_history_block .note-list-comment';
    }

    /**
     * Comment History
     *
     * @return array|string
     */
    public function getCommentHistory()
    {
        return $this->_rootElement->find($this->_commentHistoryClass, Locator::SELECTOR_CLASS_NAME)->getText();
    }

    /**
     * Get Authorized Amount
     *
     * @return string
     */
    public function getAuthorizedAmount()
    {
        return $this->_rootElement->find($this->_authorizedAmount)->getText();
    }
}
