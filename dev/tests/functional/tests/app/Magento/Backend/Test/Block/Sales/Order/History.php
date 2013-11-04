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
    private $commentHistory;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->commentHistory = '.note-list-comment';
    }

    /**
     * Get comments history
     *
     * @return string
     */
    public function getCommentsHistory()
    {
        return $this->_rootElement->find($this->commentHistory)->getText();
    }
}
