<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Email\Controller\Adminhtml\Email\Template;

class Index extends \Magento\Email\Controller\Adminhtml\Email\Template
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Email Templates'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Email::template');
        $this->_addBreadcrumb(__('Transactional Emails'), __('Transactional Emails'));
        $this->_view->renderLayout();
    }
}
