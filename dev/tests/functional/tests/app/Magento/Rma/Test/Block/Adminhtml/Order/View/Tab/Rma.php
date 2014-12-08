<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Order\View\Tab;

/**
 * Class Returns
 * Order Returns block
 *
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
    protected $filters = [
        'id' => [
            'selector' => '#order_rma_filter_increment_id_to',
        ],
    ];
}
