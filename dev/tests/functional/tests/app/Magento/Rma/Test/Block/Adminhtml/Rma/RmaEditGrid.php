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

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Returns
 * Order Returns block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class RmaEditGrid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Returns
     *
     * @var string
     */
    protected $rmaGrid = "#magento_rma_item_edit_grid";

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_rma_filter_increment_id_to'
        ),
    );

    /**
     * Get Rma Edit Grid
     *
     * @return string
     */
    public function getReturnsContent()
    {
        return $this->_rootElement->find($this->rmaGrid, Locator::SELECTOR_XPATH);

    }
}
