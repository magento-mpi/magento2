<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Application model
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Magento_Oauth_Model_Resource_Consumer _getResource()
 * @method Magento_Oauth_Model_Resource_Consumer getResource()
 * @method Magento_Oauth_Model_Resource_Consumer_Collection getCollection()
 * @method Magento_Oauth_Model_Resource_Consumer_Collection getResourceCollection()
 * @method string getName()
 * @method Magento_Oauth_Model_Consumer setName() setName(string $name)
 * @method Magento_Oauth_Model_Consumer setKey() setKey(string $key)
 * @method Magento_Oauth_Model_Consumer setSecret() setSecret(string $secret)
 * @method Magento_Oauth_Model_Consumer setCallbackUrl() setCallbackUrl(string $url)
 * @method string getCreatedAt()
 * @method Magento_Oauth_Model_Consumer setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method Magento_Oauth_Model_Consumer setUpdatedAt() setUpdatedAt(string $date)
 * @method string getRejectedCallbackUrl()
 * @method Magento_Oauth_Model_Consumer setRejectedCallbackUrl() setRejectedCallbackUrl(string $rejectedCallbackUrl)
 * @method string getHttpPostUrl()
 * @method Magento_Oauth_Model_Consumer setHttpPostUrl() setHttpPostUrl(string $httpPostUrl)
 */
class Magento_Oauth_Model_Consumer extends Magento_Core_Model_Abstract
{
    /**
     * Key hash length
     */
    const KEY_LENGTH = 32;

    /**
     * Secret hash length
     */
    const SECRET_LENGTH = 32;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Oauth_Model_Resource_Consumer');
    }

    /**
     * BeforeSave actions
     *
     * @return Magento_Oauth_Model_Consumer
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setUpdatedAt(time());
        }
        $this->validate();
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return array|bool
     * @throw Magento_Core_Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        if ($this->getCallbackUrl() || $this->getRejectedCallbackUrl()) {
            $this->setCallbackUrl(trim($this->getCallbackUrl()));
            $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));

            /** @var $validatorUrl Magento_Core_Model_Url_Validator */
            $validatorUrl = Mage::getSingleton('Magento_Core_Model_Url_Validator');

            if ($this->getCallbackUrl() && !$validatorUrl->isValid($this->getCallbackUrl())) {
                Mage::throwException(__('Invalid Callback URL'));
            }
            if ($this->getRejectedCallbackUrl() && !$validatorUrl->isValid($this->getRejectedCallbackUrl())) {
                Mage::throwException(__('Invalid Rejected Callback URL'));
            }
        }

        /** @var $validatorLength Magento_Oauth_Model_Consumer_Validator_KeyLength */
        $validatorLength = Mage::getModel('Magento_Oauth_Model_Consumer_Validator_KeyLength',
            array('options' => array(
                'length' => self::KEY_LENGTH
            )));

        $validatorLength->setName('Consumer Key');
        if (!$validatorLength->isValid($this->getKey())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::SECRET_LENGTH);
        $validatorLength->setName('Consumer Secret');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }
        return true;
    }

    /**
     * Load consumer by key.
     *
     * @param string $key
     * @return Magento_Oauth_Model_Consumer
     */
    public function loadByKey($key)
    {
        return $this->load($key, 'key');
    }

    /**
     * Get consumer key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getData('key');
    }

    /**
     * Get consumer secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getData('secret');
    }

    /**
     * Get consumer callback URL.
     *
     * @return string
     */
    public function getCallBackUrl()
    {
        return $this->getData('callback_url');
    }
}
