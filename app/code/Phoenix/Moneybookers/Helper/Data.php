<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Helper_Data extends Mage_Payment_Helper_Data
{
    const XML_PATH_EMAIL        = 'moneybookers/settings/moneybookers_email';
    const XML_PATH_CUSTOMER_ID  = 'moneybookers/settings/customer_id';
    const XML_PATH_SECRET_KEY   = 'moneybookers/settings/secret_key';

    /**
     * Internal parameters for validation
     */
    protected $_moneybookersServer           = 'https://www.moneybookers.com';
    protected $_checkEmailUrl                = '/app/email_check.pl';
    protected $_checkEmailCustId             = '6999315';
    protected $_checkEmailPassword           = 'a4ce5a98a8950c04a3d34a2e2cb8c89f';
    protected $_checkSecretUrl               = '/app/secret_word_check.pl';
    protected $_activationEmailTo            = 'ecommerce@moneybookers.com';
    protected $_activationEmailSubject       = 'Magento Moneybookers Activation';
    protected $_moneybookersMasterCustId     = '7283403';
    protected $_moneybookersMasterSecretHash = 'c18524b6b1082653039078a4700367f0';

    /**
     * Send activation Email to Moneybookers
     */
    public function activateEmail()
    {
        $storeId = Mage::app()->getStore()->getId();

        $translate = Mage::getSingleton('Mage_Core_Model_Translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        Mage::getModel('Mage_Core_Model_Email_Template')
            ->setDesignConfig(array('area' => Mage_Core_Model_App_Area::AREA_FRONTEND, 'store' => $storeId))
            ->sendTransactional(
                'moneybookers_activateemail',
                Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
                $this->_activationEmailTo,
                null,
                array(
                    'subject'     => $this->_activationEmailSubject,
                    'email_addr'  => Mage::getStoreConfig(self::XML_PATH_EMAIL),
                    'url'         => Mage::getBaseUrl(),
                    'customer_id' => Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ID),
                    'language'    => Mage::getModel('Mage_Core_Model_Locale')->getDefaultLocale()
                )
            );

        $translate->setTranslateInline(true);
    }

    /**
     * Check if email is registered at Moneybookers
     *
     * @param array $params
     * @return array
     */
    public function checkEmailRequest(Array $params) {
        $response = null;
        try {
            $response = $this->_getHttpsPage($this->_moneybookersServer . $this->_checkEmailUrl, array(
                'email'    => $params['email'],
                'cust_id'  => $this->_checkEmailCustId,
                'password' => $this->_checkEmailPassword)
            );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $response;
    }

    /**
     * Check if entered secret is valid
     * @param array $params
     * @return array
     */
    public function checkSecretRequest(Array $params)
    {
        $response = null;
        try {
            $response = $this->_getHttpsPage($this->_moneybookersServer . $this->_checkSecretUrl, array(
                'email'   => $params['email'],
                'secret'  => md5(md5($params['secret']) . $this->_moneybookersMasterSecretHash),
                'cust_id' => $this->_moneybookersMasterCustId)
            );
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $response;
    }

    /**
     * Reading a page via HTTPS and returning its content.
     */
    protected function _getHttpsPage($host, $parameter)
    {
        $client = new Varien_Http_Client();
        $client->setUri($host)
            ->setConfig(array('timeout' => 30))
            ->setHeaders('accept-encoding', '')
            ->setParameterGet($parameter)
            ->setMethod(Zend_Http_Client::GET);
        $request = $client->request();
        // Workaround for pseudo chunked messages which are yet too short, so
        // only an exception is is thrown instead of returning raw body
        if (!preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $request->getRawBody(), $m))
            return $request->getRawBody();

        return $request->getBody();
    }
}
