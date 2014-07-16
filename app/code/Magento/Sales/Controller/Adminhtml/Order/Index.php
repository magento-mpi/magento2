<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

class Index extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Orders grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Orders'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
