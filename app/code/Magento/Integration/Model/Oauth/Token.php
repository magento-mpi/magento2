<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Oauth;

use Magento\Framework\Oauth\Helper\Oauth as OauthHelper;
use Magento\Integration\Model\Resource\Oauth\Token\Collection as TokenCollection;
use Magento\Framework\Oauth\Exception as OauthException;
use Magento\Authz\Model\UserIdentifier;

/**
 * oAuth token model
 *
 * @method string getName() Consumer name (joined from consumer table)
 * @method TokenCollection getCollection()
 * @method TokenCollection getResourceCollection()
 * @method \Magento\Integration\Model\Resource\Oauth\Token getResource()
 * @method \Magento\Integration\Model\Resource\Oauth\Token _getResource()
 * @method int getConsumerId()
 * @method Token setConsumerId() setConsumerId(int $consumerId)
 * @method int getUserType()
 * @method Token setUserType() setUserType(int $userType)
 * @method string getType()
 * @method Token setType() setType(string $type)
 * @method string getCallbackUrl()
 * @method Token setCallbackUrl() setCallbackUrl(string $callbackUrl)
 * @method string getCreatedAt()
 * @method Token setCreatedAt() setCreatedAt(string $createdAt)
 * @method string getToken()
 * @method Token setToken() setToken(string $token)
 * @method string getSecret()
 * @method Token setSecret() setSecret(string $tokenSecret)
 * @method int getRevoked()
 * @method Token setRevoked() setRevoked(int $revoked)
 * @method int getAuthorized()
 * @method Token setAuthorized() setAuthorized(int $authorized)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Token extends \Magento\Framework\Model\AbstractModel
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST = 'request';

    const TYPE_ACCESS = 'access';

    const TYPE_VERIFIER = 'verifier';

    /**#@- */

    /**
     * @var OauthHelper
     */
    protected $_oauthHelper;

    /**
     * @var \Magento\Integration\Helper\Oauth\Data
     */
    protected $_oauthData;

    /**
     * @var \Magento\Integration\Model\Oauth\Consumer\Factory
     */
    protected $_consumerFactory;

    /**
     * @var \Magento\Framework\Url\Validator
     */
    protected $_urlValidator;

    /**
     * @var Consumer\Validator\KeyLengthFactory
     */
    protected $_keyLengthFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLengthFactory $keyLengthFactory
     * @param \Magento\Framework\Url\Validator $urlValidator
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Integration\Model\Oauth\Consumer\Factory $consumerFactory
     * @param \Magento\Integration\Helper\Oauth\Data $oauthData
     * @param OauthHelper $oauthHelper
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLengthFactory $keyLengthFactory,
        \Magento\Framework\Url\Validator $urlValidator,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Integration\Model\Oauth\Consumer\Factory $consumerFactory,
        \Magento\Integration\Helper\Oauth\Data $oauthData,
        OauthHelper $oauthHelper,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_keyLengthFactory = $keyLengthFactory;
        $this->_urlValidator = $urlValidator;
        $this->_dateTime = $dateTime;
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
        $this->_init('Magento\Integration\Model\Resource\Oauth\Token');
    }

    /**
     * The "After save" actions
     *
     * @return $this
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
     * @return $this
     */
    public function createVerifierToken($consumerId)
    {
        $tokenData = $this->getResource()->selectTokenByType($consumerId, self::TYPE_VERIFIER);
        $this->setData($tokenData ? $tokenData : array());
        if (!$this->getId()) {
            $this->setData(
                array(
                    'consumer_id' => $consumerId,
                    'type' => self::TYPE_VERIFIER,
                    'token' => $this->_oauthHelper->generateToken(),
                    'secret' => $this->_oauthHelper->generateTokenSecret(),
                    'verifier' => $this->_oauthHelper->generateVerifier(),
                    'callback_url' => OauthHelper::CALLBACK_ESTABLISHED,
                    'user_type' => UserIdentifier::USER_TYPE_INTEGRATION //As of now only integrations use Oauth
                )
            );
            $this->save();
        }
        return $this;
    }

    /**
     * Convert token to access type
     *
     * @return $this
     * @throws OauthException
     */
    public function convertToAccess()
    {
        if (self::TYPE_REQUEST != $this->getType()) {
            throw new OauthException('Cannot convert to access token due to token is not request type');
        }
        return $this->saveAccessToken(UserIdentifier::USER_TYPE_INTEGRATION);
    }

    /**
     * Create access token for a admin
     *
     * @param int $userId
     * @return $this
     */
    public function createAdminToken($userId)
    {
        return $this->saveAccessToken(UserIdentifier::USER_TYPE_ADMIN, $userId);
    }

    /**
     * Create access token for a customer
     *
     * @param int $userId
     * @return $this
     */
    public function createCustomerToken($userId)
    {
        return $this->saveAccessToken(UserIdentifier::USER_TYPE_CUSTOMER, $userId);
    }

    /**
     * Generate and save request token
     *
     * @param int $entityId Token identifier
     * @param string $callbackUrl Callback URL
     * @return $this
     */
    public function createRequestToken($entityId, $callbackUrl)
    {
        $callbackUrl = !empty($callbackUrl) ? $callbackUrl : OauthHelper::CALLBACK_ESTABLISHED;
        $this->setData(
            array(
                'entity_id' => $entityId,
                'type' => self::TYPE_REQUEST,
                'token' => $this->_oauthHelper->generateToken(),
                'secret' => $this->_oauthHelper->generateTokenSecret(),
                'callback_url' => $callbackUrl
            )
        );
        $this->save();

        return $this;
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
     * @return $this
     */
    protected function _beforeSave()
    {
        $this->validate();

        if ($this->isObjectNew() && null === $this->getCreatedAt()) {
            $this->setCreatedAt($this->_dateTime->now());
        }
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return bool
     * @throws OauthException Throw exception on fail validation
     */
    public function validate()
    {
        if (OauthHelper::CALLBACK_ESTABLISHED != $this->getCallbackUrl() && !$this->_urlValidator->isValid(
            $this->getCallbackUrl()
        )
        ) {
            $messages = $this->_urlValidator->getMessages();
            throw new OauthException(array_shift($messages));
        }

        /** @var $validatorLength \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLength */
        $validatorLength = $this->_keyLengthFactory->create();
        $validatorLength->setLength(OauthHelper::LENGTH_TOKEN_SECRET);
        $validatorLength->setName('Token Secret Key');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            throw new OauthException(array_shift($messages));
        }

        $validatorLength->setLength(OauthHelper::LENGTH_TOKEN);
        $validatorLength->setName('Token Key');
        if (!$validatorLength->isValid($this->getToken())) {
            $messages = $validatorLength->getMessages();
            throw new OauthException(array_shift($messages));
        }

        if (null !== ($verifier = $this->getVerifier())) {
            $validatorLength->setLength(OauthHelper::LENGTH_TOKEN_VERIFIER);
            $validatorLength->setName('Verifier Key');
            if (!$validatorLength->isValid($verifier)) {
                $messages = $validatorLength->getMessages();
                throw new OauthException(array_shift($messages));
            }
        }
        return true;
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
     * Generate and save access token for a given user type
     *
     * @param int $userType
     * @param int $userId
     * @return $this
     */
    protected function saveAccessToken($userType, $userId = null)
    {
        $this->setUserType($userType);
        if ($userId) {
            $this->setConsumerId($userId);
        }
        $this->setType(self::TYPE_ACCESS);
        $this->setToken($this->_oauthHelper->generateToken());
        $this->setSecret($this->_oauthHelper->generateTokenSecret());
        return $this->save();
    }

    /**
     * Get token by consumer and user type
     *
     * @param int $consumerId
     * @param int $userType
     * @return $this
     */
    public function loadByConsumerIdAndUserType($consumerId, $userType)
    {
        $tokenData = $this->getResource()->selectTokenByConsumerAndUserType($consumerId, $userType);
        $this->setData($tokenData ? $tokenData : []);
        return $this;
    }
}
