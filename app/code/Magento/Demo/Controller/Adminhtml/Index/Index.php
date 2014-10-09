<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Demo\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Index extends Action
{
    public function execute()
    {
        $this->_title->add(__('Demo'));

        $this->_view->loadLayout();

        $this->_addBreadcrumb(__('Demo'), __('Demo'));
        $this->_addBreadcrumb(__('Firedrakes Demo'), __('Firedrakes Demo'));
        $this->_view->renderLayout();

    }
}