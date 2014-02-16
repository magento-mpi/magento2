<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Encryption;

/**
 * Provides basic logic for hashing passwords and encrypting/decrypting misc data
 */
class Encryptor implements EncryptorInterface
{
    /**
     * Crypt key
     */
    const PARAM_CRYPT_KEY = 'crypt.key';

    /**
     * @var \Magento\Math\Random
     */
    protected $_randomGenerator;

    /**
     * Cryptographic key
     *
     * @var string
     */
    protected $_cryptKey;

    /**
     * @var \Magento\Encryption\CryptFactory
     */
    protected $_cryptFactory;

    /**
     * @var \Magento\Encryption\Crypt
     */
    protected $_crypt;

    /**
     * @param \Magento\Math\Random $randomGenerator
     * @param \Magento\Encryption\CryptFactory $cryptFactory
     * @param string $cryptKey
     */
    public function __construct(
        \Magento\Math\Random $randomGenerator,
        \Magento\Encryption\CryptFactory $cryptFactory,
        $cryptKey
    ) {
        $this->_randomGenerator = $randomGenerator;
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
     * @param bool|int|string $salt
     * @return string
     */
    public function getHash($password, $salt = false)
    {
        if (is_integer($salt)) {
            $salt = $this->_randomGenerator->getRandomString($salt);
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
     * @throws \InvalidArgumentException
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
            default:
                break;
        }
        throw new \InvalidArgumentException('Invalid hash.');
    }

    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        if (empty($this->_cryptKey)) {
            return $data;
        }
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
        if (empty($this->_cryptKey)) {
            return $data;
        }

        return trim($this->_getCrypt()->decrypt(base64_decode((string)$data)));
    }

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string $key
     * @return \Magento\Encryption\Crypt
     */
    public function validateKey($key)
    {
        return $this->_getCrypt($key);
    }

    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return \Magento\Encryption\Crypt
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
}
