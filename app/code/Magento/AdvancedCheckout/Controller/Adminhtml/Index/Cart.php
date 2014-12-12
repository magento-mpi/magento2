<?php
/**
 * Quote items grid ajax callback
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class Cart extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->_processException($e);
        }
    }
}
