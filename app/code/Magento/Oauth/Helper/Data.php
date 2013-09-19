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
 * OAuth Helper
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**#@+
     * Endpoint types with appropriate routes
     */
    const ENDPOINT_AUTHORIZE_CUSTOMER        = 'oauth/authorize';
    const ENDPOINT_AUTHORIZE_ADMIN           = 'adminhtml/oauth_authorize';
    const ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE = 'oauth/authorize/simple';
    const ENDPOINT_AUTHORIZE_ADMIN_SIMPLE    = 'adminhtml/oauth_authorize/simple';
    const ENDPOINT_INITIATE                  = 'oauth/initiate';
    const ENDPOINT_TOKEN                     = 'oauth/token';
    /**#@-*/

    /**#@+
     * Cleanup xpath config settings
     */
    const XML_PATH_CLEANUP_PROBABILITY       = 'oauth/cleanup/cleanup_probability';
    const XML_PATH_CLEANUP_EXPIRATION_PERIOD = 'oauth/cleanup/expiration_period';
    /**#@-*/

    /**#@+ Email template */
    const XML_PATH_EMAIL_TEMPLATE = 'oauth/email/template';
    const XML_PATH_EMAIL_IDENTITY = 'oauth/email/identity';
    /**#@-*/

    /**
     * Cleanup expiration period in minutes
     */
    const CLEANUP_EXPIRATION_PERIOD_DEFAULT = 120;

    /**
     * Query parameter as a sign that user rejects
     */
    const QUERY_PARAM_REJECTED = 'rejected';

    /**
     * Available endpoints list
     *
     * @var array
     */
    protected $_endpoints = array(
        self::ENDPOINT_AUTHORIZE_CUSTOMER,
        self::ENDPOINT_AUTHORIZE_ADMIN,
        self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE,
        self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE,
        self::ENDPOINT_INITIATE,
        self::ENDPOINT_TOKEN
    );

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Generate random string for token or secret or verifier
     *
     * @param int $length String length
     * @return string
     */
    protected function _generateRandomString($length)
    {
        return $this->_coreData->getRandomString(
            $length, \Magento\Core\Helper\Data::CHARS_DIGITS . \Magento\Core\Helper\Data::CHARS_LOWERS
        );
    }

    /**
     * Generate random string for token
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->_generateRandomString(\Magento\Oauth\Model\Token::LENGTH_TOKEN);
    }

    /**
     * Generate random string for token secret
     *
     * @return string
     */
    public function generateTokenSecret()
    {
        return $this->_generateRandomString(\Magento\Oauth\Model\Token::LENGTH_SECRET);
    }

    /**
     * Generate random string for verifier
     *
     * @return string
     */
    public function generateVerifier()
    {
        return $this->_generateRandomString(\Magento\Oauth\Model\Token::LENGTH_VERIFIER);
    }

    /**
     * Generate random string for consumer key
     *
     * @return string
     */
    public function generateConsumerKey()
    {
        return $this->_generateRandomString(\Magento\Oauth\Model\Consumer::KEY_LENGTH);
    }

    /**
     * Generate random string for consumer secret
     *
     * @return string
     */
    public function generateConsumerSecret()
    {
        return $this->_generateRandomString(\Magento\Oauth\Model\Consumer::SECRET_LENGTH);
    }

    /**
     * Return complete callback URL or boolean FALSE if no callback provided
     *
     * @param \Magento\Oauth\Model\Token $token Token object
     * @param bool $rejected OPTIONAL Add user reject sign
     * @return bool|string
     */
    public function getFullCallbackUrl(\Magento\Oauth\Model\Token $token, $rejected = false)
    {
        $callbackUrl = $token->getCallbackUrl();

        if (\Magento\Oauth\Model\Server::CALLBACK_ESTABLISHED == $callbackUrl) {
            return false;
        }
        if ($rejected) {
            /** @var $consumer \Magento\Oauth\Model\Consumer */
            $consumer = \Mage::getModel('Magento\Oauth\Model\Consumer')->load($token->getConsumerId());

            if ($consumer->getId() && $consumer->getRejectedCallbackUrl()) {
                $callbackUrl = $consumer->getRejectedCallbackUrl();
            }
        } elseif (!$token->getAuthorized()) {
            \Mage::throwException('Token is not authorized');
        }
        $callbackUrl .= (false === strpos($callbackUrl, '?') ? '?' : '&');
        $callbackUrl .= 'oauth_token=' . $token->getToken() . '&';
        $callbackUrl .= $rejected ? self::QUERY_PARAM_REJECTED . '=1' : 'oauth_verifier=' . $token->getVerifier();

        return $callbackUrl;
    }

    /**
     * Retrieve URL of specified endpoint.
     *
     * @param string $type Endpoint type (one of ENDPOINT_ constants)
     * @return string
     * @throws \Exception    \Exception when endpoint not found
     */
    public function getProtocolEndpointUrl($type)
    {
        if (!in_array($type, $this->_endpoints)) {
            throw new \Exception('Invalid endpoint type passed.');
        }
        return rtrim(\Mage::getUrl($type), '/');
    }

    /**
     * Calculate cleanup possibility for data with lifetime property
     *
     * @return bool
     */
    public function isCleanupProbability()
    {
        // Safe get cleanup probability value from system configuration
        $configValue = (int) $this->_coreStoreConfig->getConfig(self::XML_PATH_CLEANUP_PROBABILITY);
        return $configValue > 0 ? 1 == mt_rand(1, $configValue) : false;
    }

    /**
     * Get cleanup expiration period value from system configuration in minutes
     *
     * @return int
     */
    public function getCleanupExpirationPeriod()
    {
        $minutes = (int) $this->_coreStoreConfig->getConfig(self::XML_PATH_CLEANUP_EXPIRATION_PERIOD);
        return $minutes > 0 ? $minutes : self::CLEANUP_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Send Email to Token owner
     *
     * @param string $userEmail
     * @param string $userName
     * @param string $applicationName
     * @param string $status
     */
    public function sendNotificationOnTokenStatusChange($userEmail, $userName, $applicationName, $status)
    {
        /* @var $mailTemplate \Magento\Core\Model\Email\Template */
        $mailTemplate = \Mage::getModel('Magento\Core\Model\Email\Template');

        $mailTemplate->sendTransactional(
            $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_TEMPLATE),
            $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $userEmail,
            $userName,
            array(
                'name'              => $userName,
                'email'             => $userEmail,
                'applicationName'   => $applicationName,
                'status'            => $status,

            )
        );
    }

    /**
     * Is current authorize page is simple
     *
     * @return boolean
     */
    protected function _getIsSimple()
    {
        $simple = false;
        if (stristr($this->_getRequest()->getActionName(), 'simple')
            || !is_null($this->_getRequest()->getParam('simple', null))
        ) {
            $simple = true;
        }

        return $simple;
    }

    /**
     * Get authorize endpoint url
     *
     * @param string $userType
     * @throws Exception
     * @return string
     */
    public function getAuthorizeUrl($userType)
    {
        $simple = $this->_getIsSimple();

        if (\Magento\Oauth\Model\Token::USER_TYPE_CUSTOMER == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER;
            }
        } elseif (\Magento\Oauth\Model\Token::USER_TYPE_ADMIN == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN;
            }
        } else {
            throw new \Exception('Invalid user type.');
        }

        return $this->_getUrl($route, array('_query' => array('oauth_token' => $this->getOauthToken())));
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    public function getOauthToken()
    {
        return $this->_getRequest()->getParam('oauth_token', null);
    }
}
