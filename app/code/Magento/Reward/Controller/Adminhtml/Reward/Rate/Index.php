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
        $this->_title->add(__('Reward Exchange Rates'));

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
