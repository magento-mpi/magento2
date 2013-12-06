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
 * Class Returns
 * Order Returns block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class Returns extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Returns
     *
     * @var string
     */
    protected $rmaLink = "//td[contains(@class, 'col-rma-number')]";

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_rma_filter_increment_id_to'
        ),
    );

    /**
     * Get Returns Grid
     *
     * @return string
     */
    public function getReturnsContent($returnId)
    {
        $this->searchAndOpen();
        //return $this->_rootElement->find($this->rmaLink, Locator::SELECTOR_XPATH)->click();

    }
}
