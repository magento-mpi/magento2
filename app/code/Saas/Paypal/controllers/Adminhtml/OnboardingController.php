<?php
/**
 * Magento Saas Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
            return Mage::getModel('saas_paypal/boarding_onboarding');
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

