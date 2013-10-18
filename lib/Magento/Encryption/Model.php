<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Provides basic logic for hashing passwords and encrypting/decrypting misc data
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Encryption;

class Model implements EncryptionInterface
{
    const PARAM_CRYPT_KEY = 'crypt.key';

    /**
     * @var \Magento\Crypt
     */
    protected $_crypt;

    /**
     * Cryptographic key
     *
     * @var string
     */
    protected $_cryptKey;

    /**
     * @var \Magento\CryptFactory
     */
    protected $_cryptFactory;

    /**
     * @param \Magento\CryptFactory $cryptFactory
     * @param string $cryptKey
     */
    public function __construct(
        \Magento\CryptFactory $cryptFactory,
        $cryptKey
    ) {
        $this->_cryptFactory = $cryptFactory;
        $this->_cryptKey = $cryptKey;
    }

    /**
     * Generate a [salted] hash.
     *
     * $salt can be:
     * false - a random will be generated
     * integer - a random with specified length will be generated
     * string
     *
     * @param string $password
     * @param mixed $salt
     * @return string
     */
    public function getHash($password, $salt = false)
    {
        if (is_integer($salt)) {
            $salt = $this->_getRandomString($salt);
        }
        return $salt === false ? $this->hash($password) : $this->hash($salt . $password) . ':' . $salt;
    }

    /**
     * Hash a string
     *
     * @param string $data
     * @return string
     */
    public function hash($data)
    {
        return md5($data);
    }

    /**
     * Validate hash against hashing method (with or without salt)
     *
     * @param string $password
     * @param string $hash
     * @throws \Magento\Core\Exception
     * @return bool
     */
    public function validateHash($password, $hash)
    {
        $hashArr = explode(':', $hash);
        switch (count($hashArr)) {
            case 1:
                return $this->hash($password) === $hash;
            case 2:
                return $this->hash($hashArr[1] . $password) === $hashArr[0];
        }
        throw new \Magento\Core\Exception('Invalid hash.');
    }

    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return \Magento\Crypt
     */
    protected function _getCrypt($key = null)
    {
        if ($key === null) {
            if (!$this->_crypt) {
                $this->_crypt = $this->_cryptFactory->create(array('key' => $this->_cryptKey));
            }

            return $this->_crypt;
        } else {
            return $this->_cryptFactory->create(array('key' => $key));
        }
    }

    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return base64_encode($this->_getCrypt()->encrypt((string)$data));
    }

    /**
     * Decrypt a string
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return trim($this->_getCrypt()->decrypt(base64_decode((string)$data)));
    }

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string $key
     * @return \Magento\Crypt
     */
    public function validateKey($key)
    {
        return $this->_getCrypt($key);
    }

    /**
     * Get random string composed of chars given
     *
     * TODO: Replace this function with lib/Math/Random invocation when it's ready
     *
     * @param int $length
     * @param null $chars
     * @return string
     */
    protected function _getRandomString($length, $chars = null)
    {
        if (is_null($chars)) {
            $chars = \Magento\Core\Helper\Data::CHARS_LOWERS . \Magento\Core\Helper\Data::CHARS_UPPERS
                . \Magento\Core\Helper\Data::CHARS_DIGITS;
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
}
