<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Queue;

class Drop extends \Magento\Newsletter\Controller\Adminhtml\Queue
{
    /**
     * Drop Newsletter queue template
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout('newsletter_queue_preview_popup');
        $this->_view->renderLayout();
    }
}
