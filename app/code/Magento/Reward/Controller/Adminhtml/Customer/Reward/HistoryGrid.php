<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Controller\Adminhtml\Customer\Reward;

class HistoryGrid extends \Magento\Reward\Controller\Adminhtml\Customer\Reward
{
    /**
     * History Grid Ajax Action
     *
     * @return void
     *
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
