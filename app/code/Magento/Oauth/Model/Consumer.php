<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Application model
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method \Magento\Oauth\Model\Resource\Consumer _getResource()
 * @method \Magento\Oauth\Model\Resource\Consumer getResource()
 * @method \Magento\Oauth\Model\Resource\Consumer\Collection getCollection()
 * @method \Magento\Oauth\Model\Resource\Consumer\Collection getResourceCollection()
 * @method string getName()
 * @method \Magento\Oauth\Model\Consumer setName() setName(string $name)
 * @method string getKey()
 * @method \Magento\Oauth\Model\Consumer setKey() setKey(string $key)
 * @method \Magento\Oauth\Model\Consumer setSecret() setSecret(string $secret)
 * @method \Magento\Oauth\Model\Consumer setCallbackUrl() setCallbackUrl(string $url)
 * @method string getCreatedAt()
 * @method \Magento\Oauth\Model\Consumer setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method \Magento\Oauth\Model\Consumer setUpdatedAt() setUpdatedAt(string $date)
 * @method string getRejectedCallbackUrl()
 * @method \Magento\Oauth\Model\Consumer setRejectedCallbackUrl() setRejectedCallbackUrl(string $rejectedCallbackUrl)
 */
namespace Magento\Oauth\Model;

abstract class Consumer extends \Magento\Core\Model\AbstractModel implements \Magento\Oauth\Model\ConsumerInterface
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
        $this->_init('Magento\Oauth\Model\Resource\Consumer');
    }

    /**
     * BeforeSave actions
     *
     * @return \Magento\Oauth\Model\Consumer
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
     * @throw \Magento\Core\Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        if ($this->getCallbackUrl() || $this->getRejectedCallbackUrl()) {
            $this->setCallbackUrl(trim($this->getCallbackUrl()));
            $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));

            /** @var $validatorUrl \Magento\Core\Model\Url\Validator */
            $validatorUrl = \Mage::getSingleton('Magento\Core\Model\Url\Validator');

            if ($this->getCallbackUrl() && !$validatorUrl->isValid($this->getCallbackUrl())) {
                \Mage::throwException(__('Invalid Callback URL'));
            }
            if ($this->getRejectedCallbackUrl() && !$validatorUrl->isValid($this->getRejectedCallbackUrl())) {
                \Mage::throwException(__('Invalid Rejected Callback URL'));
            }
        }

        /** @var $validatorLength \Magento\Oauth\Model\Consumer\Validator\KeyLength */
        $validatorLength = \Mage::getModel('Magento\Oauth\Model\Consumer\Validator\KeyLength',
            array('options' => array(
                'length' => self::KEY_LENGTH
            )));

        $validatorLength->setName('Consumer Key');
        if (!$validatorLength->isValid($this->getKey())) {
            $messages = $validatorLength->getMessages();
            \Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::SECRET_LENGTH);
        $validatorLength->setName('Consumer Secret');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            \Mage::throwException(array_shift($messages));
        }
        return true;
    }

    /**
     * Load consumer by key.
     *
     * @param string $key
     * @return \Magento\Oauth\Model\Consumer
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
