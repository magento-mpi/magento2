<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Observer_Backend_BillingAgreement
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(Magento_AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Block admin ability to use customer billing agreements
     *
     * @param Magento_Event_Observer $observer
     */
    public function dispatch($observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();
        if ($methodInstance instanceof Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract
            && false == $this->_authorization->isAllowed('Magento_Sales::use')
        ) {
            $event->getResult()->isAvailable = false;
        }
    }
}
