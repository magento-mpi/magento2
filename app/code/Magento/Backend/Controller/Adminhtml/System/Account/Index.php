<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Account;

class Index extends \Magento\Backend\Controller\Adminhtml\System\Account
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('My Account'));

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
