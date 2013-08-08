<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Application model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_Oauth_Model_Resource_Consumer _getResource()
 * @method Mage_Oauth_Model_Resource_Consumer getResource()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getResourceCollection()
 * @method string getName()
 * @method Mage_Oauth_Model_Consumer setName() setName(string $name)
 * @method string getKey()
 * @method Mage_Oauth_Model_Consumer setKey() setKey(string $key)
 * @method Mage_Oauth_Model_Consumer setSecret() setSecret(string $secret)
 * @method Mage_Oauth_Model_Consumer setCallbackUrl() setCallbackUrl(string $url)
 * @method string getCreatedAt()
 * @method Mage_Oauth_Model_Consumer setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method Mage_Oauth_Model_Consumer setUpdatedAt() setUpdatedAt(string $date)
 * @method string getRejectedCallbackUrl()
 * @method Mage_Oauth_Model_Consumer setRejectedCallbackUrl() setRejectedCallbackUrl(string $rejectedCallbackUrl)
 */
abstract class Mage_Oauth_Model_Consumer extends Mage_Core_Model_Abstract implements Mage_Oauth_Model_ConsumerInterface
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
        $this->_init('Mage_Oauth_Model_Resource_Consumer');
    }

    /**
     * BeforeSave actions
     *
     * @return Mage_Oauth_Model_Consumer
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
     * @throw Mage_Core_Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        if ($this->getCallbackUrl() || $this->getRejectedCallbackUrl()) {
            $this->setCallbackUrl(trim($this->getCallbackUrl()));
            $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));

            /** @var $validatorUrl Mage_Core_Model_Url_Validator */
            $validatorUrl = Mage::getSingleton('Mage_Core_Model_Url_Validator');

            if ($this->getCallbackUrl() && !$validatorUrl->isValid($this->getCallbackUrl())) {
                Mage::throwException(__('Invalid Callback URL'));
            }
            if ($this->getRejectedCallbackUrl() && !$validatorUrl->isValid($this->getRejectedCallbackUrl())) {
                Mage::throwException(__('Invalid Rejected Callback URL'));
            }
        }

        /** @var $validatorLength Mage_Oauth_Model_Consumer_Validator_KeyLength */
        $validatorLength = Mage::getModel('Mage_Oauth_Model_Consumer_Validator_KeyLength',
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
     * @return Mage_Oauth_Model_Consumer
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
