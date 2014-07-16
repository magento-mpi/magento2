<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Create;

use \Magento\Backend\App\Action;

class ShowUpdateResult extends \Magento\Sales\Controller\Adminhtml\Order\Create
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
