<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

class Index extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * Index Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Reward Exchange Rates'));
        $this->_view->renderLayout();
    }
}
