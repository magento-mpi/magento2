<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth\Model;

/**
 * oAuth token model
 *
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
class Token extends \Magento\Core\Model\AbstractModel
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST = 'request';
    const TYPE_ACCESS = 'access';
    const TYPE_VERIFIER = 'verifier';
    /**#@- */

    /**#@+
     * Customer types
     */
    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_CUSTOMER = 'customer';
    /**#@- */

    /** @var \Magento\Oauth\Helper\Oauth */
    protected $_oauthHelper;

    /** @var \Magento\Oauth\Helper\Data */
    protected $_oauthData;

    /** @var \Magento\Oauth\Model\Consumer\Factory */
    protected $_consumerFactory;

    /** @var \Magento\Core\Model\Url\Validator */
    protected $_urlValidator;

    /** @var Consumer\Validator\KeyLengthFactory */
    protected $_keyLengthFactory;

    /**
     * @param \Magento\Oauth\Model\Consumer\Validator\KeyLengthFactory $keyLengthFactory
     * @param \Magento\Core\Model\Url\Validator $urlValidator
     * @param \Magento\Oauth\Model\Consumer\Factory $consumerFactory
     * @param \Magento\Oauth\Helper\Data $oauthData
     * @param \Magento\Oauth\Helper\Oauth $oauthHelper
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Oauth\Model\Consumer\Validator\KeyLengthFactory $keyLengthFactory,
        \Magento\Core\Model\Url\Validator $urlValidator,
        \Magento\Oauth\Model\Consumer\Factory $consumerFactory,
        \Magento\Oauth\Helper\Data $oauthData,
        \Magento\Oauth\Helper\Oauth $oauthHelper,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_keyLengthFactory = $keyLengthFactory;
        $this->_urlValidator = $urlValidator;
        $this->_consumerFactory = $consumerFactory;
        $this->_oauthData = $oauthData;
        $this->_oauthHelper = $oauthHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Oauth\Model\Resource\Token');
    }

    /**
     * "After save" actions
     *
     * @return \Magento\Oauth\Model\Token
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        // Cleanup old entries
        if ($this->_oauthData->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($this->_oauthData->getCleanupExpirationPeriod());
        }
        return $this;
    }

    /**
     * Generate an oauth_verifier for a consumer, if the consumer doesn't already have one.
     *
     * @param int $consumerId - The id of the consumer associated with the verifier to be generated.
     * @return \Magento\Oauth\Model\Token
     */
    public function createVerifierToken($consumerId)
    {
        $tokenData = $this->getResource()->selectTokenByType($consumerId, self::TYPE_VERIFIER);
        $this->setData($tokenData ? $tokenData : array());
        if (!$this->getId()) {
            $this->setData(array(
                'consumer_id' => $consumerId,
                'type' => self::TYPE_VERIFIER,
                'token' => $this->_oauthHelper->generateToken(),
                'secret' => $this->_oauthHelper->generateTokenSecret(),
                'verifier' => $this->_oauthHelper->generateVerifier(),
                'callback_url' => \Magento\Oauth\Helper\Oauth::CALLBACK_ESTABLISHED
            ));
            $this->save();
        }
        return $this;
    }

    /**
     * Authorize token
     *
     * @param int $userId Authorization user identifier
     * @param string $userType Authorization user type
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    public function authorize($userId, $userType)
    {
        if (!$this->getId() || !$this->getConsumerId()) {
            throw new \Magento\Oauth\Exception(__('Token is not ready to be authorized'));
        }
        if ($this->getAuthorized()) {
            throw new \Magento\Oauth\Exception(__('Token is already authorized'));
        }
        if (self::USER_TYPE_ADMIN == $userType) {
            $this->setAdminId($userId);
        } elseif (self::USER_TYPE_CUSTOMER == $userType) {
            $this->setCustomerId($userId);
        } else {
            throw new \Magento\Oauth\Exception(__('User type is unknown'));
        }

        $this->setVerifier($this->_oauthHelper->generateVerifier());
        $this->setAuthorized(1);
        $this->save();

        $this->getResource()->cleanOldAuthorizedTokensExcept($this);

        return $this;
    }

    /**
     * Convert token to access type
     *
     * @return \Magento\Oauth\Model\Token
     * @throws \Magento\Oauth\Exception
     */
    public function convertToAccess()
    {
        if (self::TYPE_REQUEST != $this->getType()) {
            throw new \Magento\Oauth\Exception(__('Cannot convert to access token due to token is not request type'));
        }

        $this->setType(self::TYPE_ACCESS);
        $this->setToken($this->_oauthHelper->generateToken());
        $this->setSecret($this->_oauthHelper->generateTokenSecret());
        $this->save();

        return $this;
    }

    /**
     * Generate and save request token
     *
     * @param int $entityId Token identifier
     * @param string $callbackUrl Callback URL
     * @return \Magento\Oauth\Model\Token
     */
    public function createRequestToken($entityId, $callbackUrl)
    {
        $this->setData(array(
               'entity_id' => $entityId,
               'type' => self::TYPE_REQUEST,
               'token' => $this->_oauthHelper->generateToken(),
               'secret' => $this->_oauthHelper->generateTokenSecret(),
               'callback_url' => $callbackUrl
           ));
        $this->save();

        return $this;
    }

    /**
     * Get OAuth user type
     *
     * @return string
     * @throws \Magento\Oauth\Exception
     */
    public function getUserType()
    {
        if ($this->getAdminId()) {
            return self::USER_TYPE_ADMIN;
        } elseif ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        } else {
            throw new \Magento\Oauth\Exception(__('User type is unknown'));
        }
    }

    /**
     * Get string representation of token
     *
     * @param string $format
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     * @throws \Magento\Oauth\Exception Throw exception on fail validation
     */
    public function validate()
    {
        if (\Magento\Oauth\Helper\Oauth::CALLBACK_ESTABLISHED != $this->getCallbackUrl()
            && !$this->_urlValidator->isValid($this->getCallbackUrl())
        ) {
            $messages = $this->_urlValidator->getMessages();
            throw new \Magento\Oauth\Exception(array_shift($messages));
        }

        /** @var $validatorLength \Magento\Oauth\Model\Consumer\Validator\KeyLength */
        $validatorLength = $this->_keyLengthFactory->create();
        $validatorLength->setLength(\Magento\Oauth\Helper\Oauth::LENGTH_SECRET);
        $validatorLength->setName('Token Secret Key');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            throw new \Magento\Oauth\Exception(array_shift($messages));
        }

        $validatorLength->setLength(\Magento\Oauth\Helper\Oauth::LENGTH_TOKEN);
        $validatorLength->setName('Token Key');
        if (!$validatorLength->isValid($this->getToken())) {
            $messages = $validatorLength->getMessages();
            throw new \Magento\Oauth\Exception(array_shift($messages));
        }

        if (null !== ($verifier = $this->getVerifier())) {
            $validatorLength->setLength(\Magento\Oauth\Helper\Oauth::LENGTH_VERIFIER);
            $validatorLength->setName('Verifier Key');
            if (!$validatorLength->isValid($verifier)) {
                $messages = $validatorLength->getMessages();
                throw new \Magento\Oauth\Exception(array_shift($messages));
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
            $consumer = $this->_consumerFactory->create()->load($this->getConsumerId());
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
     * @return \Magento\Oauth\Model\Token
     */
    public function setVerifier($verifier)
    {
        $this->setData('verifier', $verifier);
        return $this;
    }
}
