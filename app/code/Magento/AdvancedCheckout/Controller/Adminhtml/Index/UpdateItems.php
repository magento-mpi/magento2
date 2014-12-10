<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class UpdateItems extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Mass update quote items, ajax
     * Currently not used, as all requests now go through loadBlock action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_isModificationAllowed();
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $items = $this->getRequest()->getPost('item', []);
            if ($items) {
                $this->getCartModel()->updateQuoteItems($items);
            }
            $this->getCartModel()->saveQuote();
        } catch (\Exception $e) {
            $this->_processException($e);
        }
    }
}
