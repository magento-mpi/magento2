<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class NewAction extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Create new gift wrapping
     *
     * @return void
     */
    public function execute()
    {
        $this->_initModel();
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Gift Wrapping'));
        $this->_view->renderLayout();
    }
}
