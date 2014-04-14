<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class OrderStatusIndex
 *
 * @package Magento\Sales\Test\Page\Adminhtml
 */
class OrderStatusIndex extends BackendPage
{
    const MCA = 'sales/order_status/index';

    protected $_blocks = [
        'orderStatusGrid' => [
            'name' => 'orderStatusGrid',
            'class' => 'Magento\Sales\Test\Block\Adminhtml\Order\StatusGrid',
            'locator' => '#sales_order_status_grid',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\StatusGrid
     */
    public function getOrderStatusGrid()
    {
        return $this->getBlockInstance('orderStatusGrid');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}
