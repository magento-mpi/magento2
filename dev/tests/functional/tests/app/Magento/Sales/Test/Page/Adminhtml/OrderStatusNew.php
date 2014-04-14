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
 * Class OrderStatusNew
 *
 * @package Magento\Sales\Test\Page\Adminhtml
 */
class OrderStatusNew extends BackendPage
{
    const MCA = 'sales/order_status/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'orderStatusForm' => [
            'name' => 'orderStatusForm',
            'class' => 'Magento\Sales\Test\Block\Adminhtml\Order\Status\NewStatus\OrderStatusForm',
            'locator' => '#edit_form',
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
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Status\NewStatus\OrderStatusForm
     */
    public function getOrderStatusForm()
    {
        return $this->getBlockInstance('orderStatusForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}
