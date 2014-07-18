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
        $model = $this->_initModel();
        $this->_initAction();
        $this->_title->add(__('New Gift Wrapping'));
        $this->_view->renderLayout();
    }
}
