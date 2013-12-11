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

namespace Magento\Rma\Test\Block\Adminhtml\Order\View\Tab;

/**
 * Class Returns
 * Order Returns block
 *
 * @package Magento\Rma\Test\Block\Adminhtml\Order\View\Tab
 */
class Rma extends \Magento\Backend\Test\Block\Widget\Grid
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
}
