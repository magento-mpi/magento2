<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

class BillingAgreement
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Block admin ability to use customer billing agreements
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch($observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();
        if ($methodInstance instanceof \Magento\Sales\Model\Payment\Method\Billing\AbstractAgreement
            && false == $this->_authorization->isAllowed('Magento_Sales::use')
        ) {
            $event->getResult()->isAvailable = false;
        }
    }
}
