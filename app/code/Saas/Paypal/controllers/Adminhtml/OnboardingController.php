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
    protected $_onboardingInstance;

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
                    ->setUrl($this->_getOnboarding()->getEnterBoardingUrl($method));
            } catch (Exception $e) {
                $response->setError(true)
                    ->setMessage($e->getMessage());
            }
            $this->getResponse()->setBody($response->toJson());
        }
    }

    /**
     * Getter for $_onboardingInstance
     * @return Saas_Paypal_Model_Boarding_Onboarding
     */
    protected function _getOnboarding()
    {
        if ($this->_onboardingInstance === null) {
            return Mage::getModel('Saas_Paypal_Model_Boarding_Onboarding');
        }
        return $this->_onboardingInstance;
    }

    /**
     * Setter for $_onboardingInstance
     * @param Saas_Paypal_Model_Boarding_Onboarding $instance
     */
    public function setOnboarding(Saas_Paypal_Model_Boarding_Onboarding $instance)
    {
        $this->_onboardingInstance = $instance;
    }

    /**
     * Wrapper for response encoded with json
     * @return Varien_Object
     */
    protected function _responseContainer()
    {
        return new Varien_Object();
    }
}
