<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Onboarding admin controller
 */
class Saas_Paypal_Adminhtml_OnboardingController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Saas_Paypal_Model_Boarding_Onboarding
     */
    protected $_onboarding;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_Paypal_Model_Boarding_Onboarding $onboarding
     * @param null $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_Paypal_Model_Boarding_Onboarding $onboarding,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_onboarding = $onboarding;
    }

    /**
     * Do requestPermissions operation
     */
    public function enterAction()
    {
        $method  = (string)$this->getRequest()->getParam('paymentMethod');

        if ($this->getRequest()->isAjax() && !empty($method)) {
            $response = $this->_responseContainer();

            try {
                $response->setError(false)
                    ->setUrl($this->_onboarding->getEnterBoardingUrl($method));
            } catch (Exception $e) {
                $response->setError(true)
                    ->setMessage($e->getMessage());
            }
            $this->getResponse()->setBody($response->toJson());
        }
    }

    /**
     * Wrapper for response encoded with json
     * @return Varien_Object
     */
    protected function _responseContainer()
    {
        return new Varien_Object();
    }

    /**
     * Update Paypal Boarding Status
     */
    public function updateStatusAction()
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $this->getRequest();
        $token = (string)$request->getParam('request_token');
        $code  = (string)$request->getParam('verification_code');

        if ($token && $code) {
            if ($this->_onboarding->updateMethodStatus($token, $code)) {
                $this->_session->addSuccess(
                    $this->_getHelper()->__('PayPal permissions have been successfully granted.')
                );
            } else {
                $this->_session->addError(
                    $this->_getHelper()->__('PayPal permissions haven\'t been granted due error.')
                );
            }
        }

        $this->_redirect('*/system_config/edit', array('_current' => array('section', 'website', 'store')));
    }
}
