<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

class Denied extends \Magento\Backend\App\Action
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
        if (!$this->_auth->isLoggedIn()) {
            $this->_redirect('*/auth/login');
            return;
        }
        $this->_view->loadLayout(array('default', 'adminhtml_denied'));
        $this->_view->renderLayout();
    }
}
