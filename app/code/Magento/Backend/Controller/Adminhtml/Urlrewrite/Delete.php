<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Urlrewrite;

class Delete extends \Magento\Backend\Controller\Adminhtml\Urlrewrite
{
    /**
     * URL rewrite delete action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getUrlRewrite()->getId()) {
            try {
                $this->_getUrlRewrite()->delete();
                $this->messageManager->addSuccess(__('The URL Rewrite has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while deleting URL Rewrite.'));
                $this->_redirect('adminhtml/*/edit/', array('id' => $this->_getUrlRewrite()->getId()));
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
