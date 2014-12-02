<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
