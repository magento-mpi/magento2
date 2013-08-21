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
 * oAuth token model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getName() Consumer name (joined from consumer table)
 * @method Mage_Oauth_Model_Resource_Token_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Token_Collection getResourceCollection()
 * @method Mage_Oauth_Model_Resource_Token getResource()
 * @method Mage_Oauth_Model_Resource_Token _getResource()
 * @method int getConsumerId()
 * @method Mage_Oauth_Model_Token setConsumerId() setConsumerId(int $consumerId)
 * @method int getAdminId()
 * @method Mage_Oauth_Model_Token setAdminId() setAdminId(int $adminId)
 * @method int getCustomerId()
 * @method Mage_Oauth_Model_Token setCustomerId() setCustomerId(int $customerId)
 * @method string getType()
 * @method Mage_Oauth_Model_Token setType() setType(string $type)
 * @method string getCallbackUrl()
 * @method Mage_Oauth_Model_Token setCallbackUrl() setCallbackUrl(string $callbackUrl)
 * @method string getCreatedAt()
 * @method Mage_Oauth_Model_Token setCreatedAt() setCreatedAt(string $createdAt)
 * @method string getToken()
 * @method Mage_Oauth_Model_Token setToken() setToken(string $token)
 * @method string getSecret()
 * @method Mage_Oauth_Model_Token setSecret() setSecret(string $tokenSecret)
 * @method int getRevoked()
 * @method Mage_Oauth_Model_Token setRevoked() setRevoked(int $revoked)
 * @method int getAuthorized()
 * @method Mage_Oauth_Model_Token setAuthorized() setAuthorized(int $authorized)
 */
class Mage_Oauth_Model_Token extends Mage_Core_Model_Abstract
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST  = 'request';
    const TYPE_ACCESS   = 'access';
    const TYPE_VERIFIER = 'verifier';
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
        $this->_init('Mage_Oauth_Model_Resource_Token');
    }

    /**
     * "After save" actions
     *
     * @return Mage_Oauth_Model_Token
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }

    /**
     * Generate an oauth_verifier.
     *
     * @param int $consumerId - The id of the consumer associated with the verifier to be generated.
     * @return Mage_Oauth_Model_Token
     */
    public function createVerifierToken($consumerId)
    {
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');

        $this->setType(self::TYPE_VERIFIER);
        $this->setConsumerId($consumerId);
        $this->setToken($helper->generateToken());
        $this->setSecret($helper->generateTokenSecret());
        $this->setVerifier($helper->generateVerifier());
        // TODO: What should the callback_url be and where do we get its value from?
        $this->setCallbackUrl(Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED);
        $this->save();

        return $this;
    }

    /**
     * Authorize token
     *
     * @param int $userId Authorization user identifier
     * @param string $userType Authorization user type
     * @return Mage_Oauth_Model_Token
     */
    public function authorize($userId, $userType)
    {
        if (!$this->getId() || !$this->getConsumerId()) {
            Mage::throwException('Token is not ready to be authorized');
        }
        if ($this->getAuthorized()) {
            Mage::throwException('Token is already authorized');
        }
        if (self::USER_TYPE_ADMIN == $userType) {
            $this->setAdminId($userId);
        } elseif (self::USER_TYPE_CUSTOMER == $userType) {
            $this->setCustomerId($userId);
        } else {
            Mage::throwException('User type is unknown');
        }
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');

        $this->setVerifier($helper->generateVerifier());
        $this->setAuthorized(1);
        $this->save();

        $this->getResource()->cleanOldAuthorizedTokensExcept($this);

        return $this;
    }

    /**
     * Convert token to access type
     *
     * @return Mage_Oauth_Model_Token
     */
    public function convertToAccess()
    {
        if (Mage_Oauth_Model_Token::TYPE_REQUEST != $this->getType()) {
            Mage::throwException('Can not convert due to token is not request type');
        }
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');

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
     * @return Mage_Oauth_Model_Token
     */
    public function createRequestToken($consumerId, $callbackUrl)
    {
        /** @var $helper Mage_Oauth_Helper_Data */
        $helper = Mage::helper('Mage_Oauth_Helper_Data');

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
     * @throws Exception
     */
    public function getUserType()
    {
        if ($this->getAdminId()) {
            return self::USER_TYPE_ADMIN;
        } elseif ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        } else {
            Mage::throwException('User type is unknown');
        }
    }

    /**
     * Get string representation of token
     *
     * @return string
     */
    public function toString()
    {
        return http_build_query(array('oauth_token' => $this->getToken(), 'oauth_token_secret' => $this->getSecret()));
    }

    /**
     * Before save actions
     *
     * @return Mage_Oauth_Model_Consumer
     */
    protected function _beforeSave()
    {
        $this->validate();

        if ($this->isObjectNew() && null === $this->getCreatedAt()) {
            $this->setCreatedAt(Varien_Date::now());
        }
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
        /** @var $validatorUrl Mage_Core_Model_Url_Validator */
        $validatorUrl = Mage::getSingleton('Mage_Core_Model_Url_Validator');
        if (Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED != $this->getCallbackUrl()
            && !$validatorUrl->isValid($this->getCallbackUrl())
        ) {
            $messages = $validatorUrl->getMessages();
            Mage::throwException(array_shift($messages));
        }

        /** @var $validatorLength Mage_Oauth_Model_Consumer_Validator_KeyLength */
        $validatorLength = Mage::getModel(
            'Mage_Oauth_Model_Consumer_Validator_KeyLength');
        $validatorLength->setLength(self::LENGTH_SECRET);
        $validatorLength->setName('Token Secret Key');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::LENGTH_TOKEN);
        $validatorLength->setName('Token Key');
        if (!$validatorLength->isValid($this->getToken())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        if (null !== ($verifier = $this->getVerifier())) {
            $validatorLength->setLength(self::LENGTH_VERIFIER);
            $validatorLength->setName('Verifier Key');
            if (!$validatorLength->isValid($verifier)) {
                $messages = $validatorLength->getMessages();
                Mage::throwException(array_shift($messages));
            }
        }
        return true;
    }

    /**
     * Get Token Consumer
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getConsumer()
    {
        if (!$this->getData('consumer')) {
            /** @var $consumer Mage_Oauth_Model_Consumer */
            $consumer = Mage::getModel('Mage_Oauth_Model_Consumer');
            $consumer->load($this->getConsumerId());
            $this->setData('consumer', $consumer);
        }

        return $this->getData('consumer');
    }

    /**
     * Return the token's verifier.
     *
     * @return string
     */
    public function getVerifier()
    {
        return $this->getData('verifier');
    }

    /**
     * Set the token's verifier.
     *
     * @param string $verifier
     * @return Mage_Oauth_Model_Token
     */
    public function setVerifier($verifier)
    {
        $this->setData('verifier', $verifier);
        return $this;
    }
}
