<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Subscriber;

class Index extends \Magento\Newsletter\Controller\Adminhtml\Subscriber
{
    /**
     * Newsletter subscribers page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Newsletter Subscribers'));

        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Newsletter::newsletter_subscriber');

        $this->_addBreadcrumb(__('Newsletter'), __('Newsletter'));
        $this->_addBreadcrumb(__('Subscribers'), __('Subscribers'));

        $this->_view->renderLayout();
    }
}
