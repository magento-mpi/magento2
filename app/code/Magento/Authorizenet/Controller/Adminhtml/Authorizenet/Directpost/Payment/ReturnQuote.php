<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Controller\Adminhtml\Authorizenet\Directpost\Payment;

class ReturnQuote extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Return quote
     *
     * @return void
     */
    protected function _returnQuote()
    {
        $directpostSession = $this->_objectManager->get('Magento\Authorizenet\Model\Directpost\Session');
        $incrementId = $directpostSession->getLastOrderIncrementId();
        if ($incrementId && $directpostSession->isCheckoutOrderIncrementIdExist($incrementId)) {
            /* @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                $directpostSession->removeCheckoutOrderIncrementId($order->getIncrementId());
            }
        }
    }

    /**
     * Return order quote by ajax
     *
     * @return void
     */
    public function execute()
    {
        $this->_returnQuote();
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(['success' => 1])
        );
    }
}
