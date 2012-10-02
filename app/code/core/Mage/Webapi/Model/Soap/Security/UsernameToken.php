<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * SOAP WS-Security UsernameToken model.
 *
 * @see http://docs.oasis-open.org/wss-m/wss/v1.1.1/os/wss-UsernameTokenProfile-v1.1.1-os.html
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Soap_Security_UsernameToken
{
    /**
     * Available password types.
     */
    const PASSWORD_TYPE_TEXT = 'PasswordText';
    const PASSWORD_TYPE_DIGEST = 'PasswordDigest';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * Username value.
     *
     * @var string
     */
    protected $_username;

    /**
     * Password value.
     *
     * @var string
     */
    protected $_password;

    /**
     * Password type value.
     *
     * @var string
     */
    protected $_passwordType = self::PASSWORD_TYPE_TEXT;

    /**
     * Token nonce.
     *
     * @var string
     */
    protected $_nonce;

    /**
     * Token created at timestamp.
     *
     * @var string
     */
    protected $_created;

    /**
     * Construct WS-Security UsernameToken object.
     *
     * @param array $options
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_MissingUsernameException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_MissingPasswordException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_MissingNonceException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_MissingCreatedException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidPasswordTypeException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException
     */
    public function __construct($options)
    {
        $this->_objectFactory = isset($options['objectFactory']) ? $options['objectFactory'] : Mage::getConfig();

        if (!isset($options['username']) || empty($options['username'])) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_MissingUsernameException;
        }
        $this->_username = $options['username'];

        if (isset($options['passwordType'])) {
            if (!in_array($options['passwordType'], array(self::PASSWORD_TYPE_DIGEST, self::PASSWORD_TYPE_TEXT))) {
                throw new Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidPasswordTypeException;
            }
            $this->_passwordType = $options['passwordType'];
        }

        if (!isset($options['password']) || empty($options['password'])) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_MissingPasswordException;
        }
        $this->_password = $options['password'];

        if (!isset($options['created']) || empty($options['created'])) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_MissingCreatedException;
        }
        $createdTimestamp = $this->_getTimestampFromDate($options['created']);
        if (!$createdTimestamp) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException;
        }
        $this->_created = $options['created'];

        if (!isset($options['nonce']) || empty($options['nonce'])) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_MissingNonceException;
        }
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage $nonceStorage */
        $nonceStorage = isset($options['nonceStorage'])
            ? $options['nonceStorage']
            : $this->_objectFactory->getModelInstance('Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage');
        $nonceStorage->validateNonce($options['nonce'], $createdTimestamp);
        $this->_nonce = $options['nonce'];
    }

    /**
     * Authenticate token and return user model.
     *
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_UserNotFoundException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_NotAuthenticatedException
     * @return Mage_Webapi_Model_Acl_User
     */
    public function authenticate()
    {
        /** @var Mage_Webapi_Model_Acl_User $user */
        $user = $this->_objectFactory->getModelInstance('Mage_Webapi_Model_Acl_User');
        if (!$user->load($this->_username, 'user_name')->getId()) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_UserNotFoundException;
        }

        $password = $user->getApiSecret();
        if ($this->_passwordType == self::PASSWORD_TYPE_DIGEST) {
            $baseString = base64_decode($this->_nonce) . $this->_created . $password;
            $password = base64_encode(hash('sha1', $baseString, true));
        }

        if ($password != $this->_password) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_NotAuthenticatedException;
        }

        return $user;
    }

    /**
     * Convert ISO8601 date string to timestamp.
     * Date format with microseconds is accepted as well.
     *
     * @param string $date
     * @return int
     */
    protected function _getTimestampFromDate($date)
    {
        $timestamp = 0;
        $dateTime = DateTime::createFromFormat(DateTime::ISO8601, $date);
        if (!$dateTime) {
            // Format with microseconds
            $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $date);
        }

        if ($dateTime) {
            $timestamp = $dateTime->format('U');
        }

        return $timestamp;
    }
}
