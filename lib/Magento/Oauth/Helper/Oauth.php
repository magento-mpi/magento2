<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth\Helper;

class Oauth
{
    /**#@+
     * Lengths of token fields
     */
    const LENGTH_TOKEN = 32;
    const LENGTH_SECRET = 32;
    const LENGTH_VERIFIER = 32;
    /**#@- */

    /**#@+
     * Lengths of consumer fields
     */
    const KEY_LENGTH = 32;
    const SECRET_LENGTH = 32;
    /**#@- */

    /**
     * Nonce length
     */
    const NONCE_LENGTH = 32;

    /**
     * Value of callback URL when it is established or if the client is unable to receive callbacks
     *
     * @link http://tools.ietf.org/html/rfc5849#section-2.1     Requirement in RFC-5849
     */
    const CALLBACK_ESTABLISHED = 'oob';

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(\Magento\Core\Helper\Data $coreData)
    {
        $this->_coreData = $coreData;
    }

    /**
     * Generate random string for token or secret or verifier
     *
     * @param int $length String length
     * @return string
     */
    public function generateRandomString($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is install. It provides a better randomness.
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $randomString = substr($hex, 0, $length); // truncate at most 1 char if length parameter is an odd number
        } else {
            // fallback to mt_rand() if openssl is not installed
            $randomString = $this->_coreData->getRandomString(
                $length,
                \Magento\Core\Helper\Data::CHARS_DIGITS . \Magento\Core\Helper\Data::CHARS_LOWERS
            );
        }

        return $randomString;
    }

    /**
     * Generate random string for token
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->generateRandomString(self::LENGTH_TOKEN);
    }

    /**
     * Generate random string for token secret
     *
     * @return string
     */
    public function generateTokenSecret()
    {
        return $this->generateRandomString(self::LENGTH_SECRET);
    }

    /**
     * Generate random string for verifier
     *
     * @return string
     */
    public function generateVerifier()
    {
        return $this->generateRandomString(self::LENGTH_VERIFIER);
    }

    /**
     * Generate random string for consumer key
     *
     * @return string
     */
    public function generateConsumerKey()
    {
        return $this->generateRandomString(self::KEY_LENGTH);
    }

    /**
     * Generate random string for consumer secret
     *
     * @return string
     */
    public function generateConsumerSecret()
    {
        return $this->generateRandomString(self::SECRET_LENGTH);
    }
}
