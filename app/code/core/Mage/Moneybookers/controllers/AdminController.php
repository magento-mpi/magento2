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
 * @category    Mage
 * @package     Mage_Moneybookers
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Moneybookers_AdminController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL        = 'moneybookers/settings/moneybookers_email';
    const XML_PATH_CUSTOMER_ID    = 'moneybookers/settings/customer_id';

    /**
     * Admin Block Type
     *
     * @var string
     */
    protected $_redirectBlockType            = 'moneybookers/admin';
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
    public function activateemailAction()
    {
        $email_addr = Mage::getStoreConfig(self::XML_PATH_EMAIL);
        $email = "Magento\n";
        $email .= 'Moneybookers Email Address: ' . $email_addr . "\n";
        $email .= 'Moneybookers Customer ID: ' . Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ID) . "\n";
        $email .= 'URL: ' . Mage::getBaseUrl() . "\n";
        $email .= 'Language: ' . Mage::getModel('core/locale')->getDefaultLocale() . "\n";

        $mail = new Zend_Mail();
        $mail->setBodyText($email);
        $mail->setSubject($this->_activationEmailSubject);
        $mail->addTo($this->_activationEmailTo);
        $mail->setFrom($email_addr);
        $mail->send();
    }

    /**
     * Check if email is registered at Moneybookers
     */
    public function checkemailAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (!isset($params['email']))
                Mage::throwException('Error: No parameters specified');

            $response = $this->_getHttpsPage($this->_moneybookersServer . $this->_checkEmailUrl, array(
                'email'    => $params['email'],
                'cust_id'  => $this->_checkEmailCustId,
                'password' => $this->_checkEmailPassword)
            );
            if (empty($response))
                Mage::throwException('Error: Connection to moneybookers.com failed');
        }
        catch (Exception $e) {
            $response = $e->getMessage();
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Check if entered secret is valid
     */
    public function checksecretAction()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (!isset($params['email']) || !isset($params['secret']))
                throw new Exception('Error: No parameters specified');

            $response = $this->_getHttpsPage($this->_moneybookersServer . $this->_checkSecretUrl, array(
                'email'   => $params['email'],
                'secret'  => md5(md5($params['secret']) . $this->_moneybookersMasterSecretHash),
                'cust_id' => $this->_moneybookersMasterCustId)
            );
            if (empty($response))
                throw new Exception('Error: Connection to moneybookers.com failed');
        }
        catch (Exception $e) {
            $response = $e->getMessage();
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Reading a page via HTTPS and returning its content.
     */
    protected function _getHttpsPage($host, $parameter)
    {
        $client = new Varien_Http_Client();
        $client->setUri($host)
            ->setConfig(array('timeout'=>30))
            ->setHeaders('accept-encoding', '')
            ->setParameterGet($parameter)
            ->setMethod(Zend_Http_Client::GET);
        $request = $client->request();
        // Workaround for pseudo chunked messages which are yet too short, so only an exception is is thrown instead of returning raw body
        if (! preg_match("/^([\da-fA-F]+)[^\r\n]*\r\n/sm", $request->getRawBody(), $m))
            return $request->getRawBody();

        return $request->getBody();
    }
}
