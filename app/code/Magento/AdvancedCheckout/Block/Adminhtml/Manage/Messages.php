<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

/**
 * Admin Checkout block for showing messages
 */
class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * Prepares layout for current block
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->messageManager->getMessages(true));
        parent::_prepareLayout();
    }
}
