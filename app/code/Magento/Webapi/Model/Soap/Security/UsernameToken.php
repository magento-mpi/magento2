<?php
/**
 * Model of SOAP WS-Security user token.
 *
 * @see http://docs.oasis-open.org/wss-m/wss/v1.1.1/os/wss-UsernameTokenProfile-v1.1.1-os.html
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Security;

class UsernameToken
{
    /**#@+
     * Available password types.
     */
    const PASSWORD_TYPE_TEXT = 'PasswordText';
    const PASSWORD_TYPE_DIGEST = 'PasswordDigest';
    /**#@-*/

    /**
     * Password type value.
     *
     * @var string
     */
    protected $_passwordType = self::PASSWORD_TYPE_TEXT;

    /**
     * Nonce storage.
     *
     * @var \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage
     */
    protected $_nonceStorage;

    /**
     * Webapi users factory.
     *
     * @var \Magento\Webapi\Model\Acl\User\Factory
     */
    protected $_userFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage $nonceStorage
     * @param \Magento\Webapi\Model\Acl\User\Factory $userFactory
     * @param string $passwordType
     * @throws \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidPasswordTypeException
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage $nonceStorage,
        \Magento\Webapi\Model\Acl\User\Factory $userFactory,
        $passwordType = self::PASSWORD_TYPE_DIGEST
    ) {
        if (!in_array($passwordType, array(self::PASSWORD_TYPE_DIGEST, self::PASSWORD_TYPE_TEXT))) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidPasswordTypeException;
        }
        $this->_passwordType = $passwordType;
        $this->_nonceStorage = $nonceStorage;
        $this->_userFactory = $userFactory;
    }

    /**
     * Authenticate username token data.
     *
     * @param string $username username value from token.
     * @param string $password password value from token.
     * @param string $created timestamp created value (must be in ISO-8601 format).
     * @param string $nonce timestamp nonce.
     * @return \Magento\Webapi\Model\Acl\User
     * @throws \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidCredentialException
     * @throws \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidDateException
     */
    public function authenticate($username, $password, $created, $nonce)
    {
        $createdTimestamp = $this->_getTimestampFromDate($created);
        if (!$createdTimestamp) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidDateException;
        }
        $this->_nonceStorage->validateNonce($nonce, $createdTimestamp);

        $user = $this->_userFactory->create();
        if (!$user->load($username, 'api_key')->getId()) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidCredentialException;
        }

        $localPassword = $user->getSecret();
        if ($this->_passwordType == self::PASSWORD_TYPE_DIGEST) {
            $baseString = base64_decode($nonce) . $created . $localPassword;
            $localPassword = base64_encode(hash('sha1', $baseString, true));
        }

        if ($localPassword != $password) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidCredentialException;
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
        $dateTime = \DateTime::createFromFormat(\DateTime::ISO8601, $date);
        if (!$dateTime) {
            // Format with microseconds
            $dateTime = \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $date);
        }

        if ($dateTime) {
            $timestamp = $dateTime->format('U');
        }

        return $timestamp;
    }
}
