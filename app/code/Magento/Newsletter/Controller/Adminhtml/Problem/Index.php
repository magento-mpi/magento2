<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Newsletter\Controller\Adminhtml\Problem;

class Index extends \Magento\Newsletter\Controller\Adminhtml\Problem
{
    /**
     * Newsletter problems report page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));

        $this->_setActiveMenu('Magento_Newsletter::newsletter_problem');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Newsletter Problems Report'));
        $this->_addBreadcrumb(__('Newsletter Problem Reports'), __('Newsletter Problem Reports'));

        $this->_view->renderLayout();
    }
}
