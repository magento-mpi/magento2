<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class OrderOptions extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Ajax action for GiftWrapping content in backend order creation
     *
     * @return void
     * @deprecated since 1.12.0.0
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
