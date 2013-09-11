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
 * oAuth token model
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getName() Consumer name (joined from consumer table)
 * @method \Magento\Oauth\Model\Resource\Token\Collection getCollection()
 * @method \Magento\Oauth\Model\Resource\Token\Collection getResourceCollection()
 * @method \Magento\Oauth\Model\Resource\Token getResource()
 * @method \Magento\Oauth\Model\Resource\Token _getResource()
 * @method int getConsumerId()
 * @method \Magento\Oauth\Model\Token setConsumerId() setConsumerId(int $consumerId)
 * @method int getAdminId()
 * @method \Magento\Oauth\Model\Token setAdminId() setAdminId(int $adminId)
 * @method int getCustomerId()
 * @method \Magento\Oauth\Model\Token setCustomerId() setCustomerId(int $customerId)
 * @method string getType()
 * @method \Magento\Oauth\Model\Token setType() setType(string $type)
 * @method string getVerifier()
 * @method \Magento\Oauth\Model\Token setVerifier() setVerifier(string $verifier)
 * @method string getCallbackUrl()
 * @method \Magento\Oauth\Model\Token setCallbackUrl() setCallbackUrl(string $callbackUrl)
 * @method string getCreatedAt()
 * @method \Magento\Oauth\Model\Token setCreatedAt() setCreatedAt(string $createdAt)
 * @method string getToken()
 * @method \Magento\Oauth\Model\Token setToken() setToken(string $token)
 * @method string getSecret()
 * @method \Magento\Oauth\Model\Token setSecret() setSecret(string $tokenSecret)
 * @method int getRevoked()
 * @method \Magento\Oauth\Model\Token setRevoked() setRevoked(int $revoked)
 * @method int getAuthorized()
 * @method \Magento\Oauth\Model\Token setAuthorized() setAuthorized(int $authorized)
 */
namespace Magento\Oauth\Model;

class Token extends \Magento\Core\Model\AbstractModel
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST = 'request';
    const TYPE_ACCESS  = 'access';
    /**#@- */

    /**#@+
     * Lengths of token fields
     */
    const LENGTH_TOKEN    = 32;
    const LENGTH_SECRET   = 32;
    const LENGTH_VERIFIER = 32;
    /**#@- */

    /**#@+
     * Customer types
     */
    const USER_TYPE_ADMIN    = 'admin';
    const USER_TYPE_CUSTOMER = 'customer';
    /**#@- */

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\Magento\Oauth\Model\Resource\Token');
    }

    /**
     * "After save" actions
     *
     * @return \Magento\Oauth\Model\Token
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }

    /**
     * Authorize token
     *
     * @param int $userId Authorization user identifier
     * @param string $userType Authorization user type
     * @return \Magento\Oauth\Model\Token
     */
    public function authorize($userId, $userType)
    {
        if (!$this->getId() || !$this->getConsumerId()) {
            \Mage::throwException('Token is not ready to be authorized');
        }
        if ($this->getAuthorized()) {
            \Mage::throwException('Token is already authorized');
        }
        if (self::USER_TYPE_ADMIN == $userType) {
            $this->setAdminId($userId);
        } elseif (self::USER_TYPE_CUSTOMER == $userType) {
            $this->setCustomerId($userId);
        } else {
            \Mage::throwException('User type is unknown');
        }
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');

        $this->setVerifier($helper->generateVerifier());
        $this->setAuthorized(1);
        $this->save();

        $this->getResource()->cleanOldAuthorizedTokensExcept($this);

        return $this;
    }

    /**
     * Convert token to access type
     *
     * @return \Magento\Oauth\Model\Token
     */
    public function convertToAccess()
    {
        if (\Magento\Oauth\Model\Token::TYPE_REQUEST != $this->getType()) {
            \Mage::throwException('Can not convert due to token is not request type');
        }
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');

        $this->setType(self::TYPE_ACCESS);
        $this->setToken($helper->generateToken());
        $this->setSecret($helper->generateTokenSecret());
        $this->save();

        return $this;
    }

    /**
     * Generate and save request token
     *
     * @param int $consumerId Consumer identifier
     * @param string $callbackUrl Callback URL
     * @return \Magento\Oauth\Model\Token
     */
    public function createRequestToken($consumerId, $callbackUrl)
    {
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');

        $this->setData(array(
            'consumer_id'  => $consumerId,
            'type'         => self::TYPE_REQUEST,
            'token'        => $helper->generateToken(),
            'secret'       => $helper->generateTokenSecret(),
            'callback_url' => $callbackUrl
        ));
        $this->save();

        return $this;
    }

    /**
     * Get OAuth user type
     *
     * @return string
     * @throws \Exception
     */
    public function getUserType()
    {
        if ($this->getAdminId()) {
            return self::USER_TYPE_ADMIN;
        } elseif ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        } else {
            \Mage::throwException('User type is unknown');
        }
    }

    /**
     * Get string representation of token
     *
     * @param string $format
     * @return string
     */
    public function toString($format = '')
    {
        return http_build_query(array('oauth_token' => $this->getToken(), 'oauth_token_secret' => $this->getSecret()));
    }

    /**
     * Before save actions
     *
     * @return \Magento\Oauth\Model\Consumer
     */
    protected function _beforeSave()
    {
        $this->validate();

        if ($this->isObjectNew() && null === $this->getCreatedAt()) {
            $this->setCreatedAt(\Magento\Date::now());
        }
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
        /** @var $validatorUrl \Magento\Core\Model\Url\Validator */
        $validatorUrl = \Mage::getSingleton('Magento\Core\Model\Url\Validator');
        if (\Magento\Oauth\Model\Server::CALLBACK_ESTABLISHED != $this->getCallbackUrl()
            && !$validatorUrl->isValid($this->getCallbackUrl())
        ) {
            $messages = $validatorUrl->getMessages();
            \Mage::throwException(array_shift($messages));
        }

        /** @var $validatorLength \Magento\Oauth\Model\Consumer\Validator\KeyLength */
        $validatorLength = \Mage::getModel(
            '\Magento\Oauth\Model\Consumer\Validator\KeyLength');
        $validatorLength->setLength(self::LENGTH_SECRET);
        $validatorLength->setName('Token Secret Key');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            \Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::LENGTH_TOKEN);
        $validatorLength->setName('Token Key');
        if (!$validatorLength->isValid($this->getToken())) {
            $messages = $validatorLength->getMessages();
            \Mage::throwException(array_shift($messages));
        }

        if (null !== ($verifier = $this->getVerifier())) {
            $validatorLength->setLength(self::LENGTH_VERIFIER);
            $validatorLength->setName('Verifier Key');
            if (!$validatorLength->isValid($verifier)) {
                $messages = $validatorLength->getMessages();
                \Mage::throwException(array_shift($messages));
            }
        }
        return true;
    }

    /**
     * Get Token Consumer
     *
     * @return \Magento\Oauth\Model\Consumer
     */
    public function getConsumer()
    {
        if (!$this->getData('consumer')) {
            /** @var $consumer \Magento\Oauth\Model\Consumer */
            $consumer = \Mage::getModel('Magento\Oauth\Model\Consumer');
            $consumer->load($this->getConsumerId());
            $this->setData('consumer', $consumer);
        }

        return $this->getData('consumer');
    }
}
