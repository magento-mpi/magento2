<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Billing Agreement method
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Method_Agreement extends Mage_Payment_Model_Method_Abstract
    implements Mage_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Manage billing agreements availability
     *
     * @var bool
     */
    protected $_canManageBillingAgreements = true;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'paypal/pro';

    /**
     * Method code
     *
     * @var string
     */
    protected $_code = Mage_Paypal_Model_Config::METHOD_BILLING_AGREEMENT;

    /**
     * Website Payments Pro instance
     *
     * @var Mage_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Initialize Mage_Paypal_Model_Pro model
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        $proInstance = array_shift($params);
        if ($proInstance && ($proInstance instanceof Mage_Paypal_Model_Pro)) {
            $this->_pro = $proInstance;
        } else {
            $this->_pro = Mage::getModel($this->_proType);
        }
        $this->_pro->setMethod($this->_code);
    }

    /**
     * Init billing agreement
     *
     * @param Varien_Object $request
     * @return Mage_Paypal_Model_Method_Agreement
     */
    public function initBillingAgreementToken(Mage_Payment_Model_Billing_Agreement $agreement)
    {
        $api = $this->_pro->getApi()
            ->setReturnUrl($agreement->getReturnUrl())
            ->setCancelUrl($agreement->getCancelUrl())
            ->setBillingType($this->_pro->getApi()->getBillingAgreementType());

        $api->callSetCustomerBillingAgreement();
        $token = $api->getToken();
        $agreement->setRedirectUrl(
            $this->_pro->getConfig()->getStartBillingAgreementUrl($token)
        );
        return $this;
    }

    /**
     * Retrieve billing agreement customer details by token
     *
     * @param Varien_Object $request
     * @return Mage_Paypal_Model_Method_Agreement
     */
    public function getBillingAgreementTokenInfo(Mage_Payment_Model_Billing_Agreement $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callGetBillingAgreementCustomerDetails();
        $agreement->addData($api->getData());
        return $this;
    }

    /**
     * Create billing agreement by token specified in request
     *
     * @param Varien_Object $request
     * @return Mage_Paypal_Model_Method_Agreement
     */
    public function placeBillingAgreement(Mage_Payment_Model_Billing_Agreement $agreement)
    {
        $api = $this->_pro->getApi()
            ->setToken($agreement->getToken());
        $api->callCreateBillingAgreement();
        $agreement->addData($api->getData());
        return $this;
    }

    /**
     * Update billing agreement status
     *
     * @param Mage_Payment_Model_Billing_Agreement $request
     * @return Mage_Paypal_Model_Method_Agreement
     */
    public function updateBillingAgreementStatus(Mage_Payment_Model_Billing_Agreement $agreement)
    {
        $api = $this->_pro->getApi()
            ->setReferenceId($agreement->getReferenceId())
            ->setBillingAgreementStatus($agreement->getStatus());
        $api->callUpdateBillingAgreement();
        return $this;
    }

}
