<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class ShowUpdateResult extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Show item update result from loadBlockAction
     * to prevent popup alert with resend data question
     *
     * @return void|false
     */
    public function execute()
    {
        $session = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($session->hasUpdateResult() && is_scalar($session->getUpdateResult())) {
            $this->getResponse()->setBody($session->getUpdateResult());
            $session->unsUpdateResult();
        } else {
            $session->unsUpdateResult();
            return false;
        }
    }
}
