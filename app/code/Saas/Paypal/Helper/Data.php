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
 * Paypal helper
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @author      Magento Saas Team <core@magentocommerce.com>
 */

class Saas_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_is_ec_credentials = null;
    protected $_is_ec_permissions = null;

    /**
     * Store is WPP credentionals activated.
     *
     * @var boolean
     */
    protected $_is_wpp_credentials =  null;

    /**
     * Store is WPP permissions activated.
     *
     * @var boolean
     */
    protected $_is_wpp_permissions = null;

    /**
     * Check if Accelerated Boarding is Active
     *
     * @return bool
     */
    public function isEcAcceleratedBoarding()
    {
        return !$this->isEcCredentials() && !$this->isEcPermissions();
    }

    /**
     * Check if Express Checkout API Credentials is Active
     *
     * @return bool
     */
    public function isEcCredentials()
    {
        if (is_null($this->_is_ec_credentials)) {
            $authMethodValue = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING . '/authentification_method'
            );
            $this->_is_ec_credentials = $authMethodValue !=
                Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS;
        }

        return $this->_is_ec_credentials;
    }

    /**
     * Check if WPP API Credentials is Active
     *
     * @return bool
     */
    public function isWppOrWppUkCredentials()
    {
        if (is_null($this->_is_wpp_credentials)) {
            $authMethodValue = (string)Mage::getStoreConfig(
                'payment/' . Mage_Paypal_Model_Config::METHOD_WPP_DIRECT . '/active'
            );
            $authMethodUkValue = (string)Mage::getStoreConfig(
                'payment/' . Mage_Paypal_Model_Config::METHOD_WPP_PE_DIRECT . '/active'
            );
            if (($authMethodValue == Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS)
                 || ($authMethodUkValue
                     == Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS)
            ) {
                $this->_is_wpp_credentials = true;
            } else {
                $this->_is_wpp_credentials = false;
            }
        }

        return $this->_is_wpp_credentials;
    }

    /**
     * Check if Express Checkout API Permissions is Active
     *
     * @return bool
     */
    public function isEcPermissions()
    {
        if (is_null($this->_is_ec_permissions)) {
            $authMethodValue = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING . '/authentification_method'
            );

            $wasActivated = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING . '/was_activated'
            );

            $ecPermissionsStatus = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING . '/status'
            );

            $this->_is_ec_permissions =
                $authMethodValue == Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS &&
                ($wasActivated || $ecPermissionsStatus == Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE);
        }

        return $this->_is_ec_permissions;
    }

    /**
     * Check if WPP Checkout API Permissions is Active
     *
     * @return bool
     */
    public function isWppPermissions()
    {
        if (is_null($this->_is_wpp_permissions)) {
            $authMethodValue = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING . '/authentification_method'
            );

            $wasActivated = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING . '/was_activated'
            );

            $ecPermissionsStatus = (string)Mage::getStoreConfig(
                'payment/' . Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING . '/status'
            );

            $this->_is_wpp_permissions =
                $authMethodValue == Saas_Paypal_Model_System_Config_Source_AuthenticationMethod::TYPE_PERMISSIONS &&
                ($wasActivated || in_array($ecPermissionsStatus, array(
                    Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_ACTIVE,
                    Saas_Paypal_Model_Boarding_Onboarding::METHOD_STATUS_PENDING
                )));
        }

        return $this->_is_wpp_permissions;
    }

    /**
     * Get redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::getSingleton('Mage_Backend_Model_Url')
            ->getUrl('*/onboarding/updateStatus', array('_current' => array('section', 'website', 'store')));
    }
}
