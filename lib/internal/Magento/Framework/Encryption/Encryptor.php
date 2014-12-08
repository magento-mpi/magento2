<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Encryption;

use Magento\Framework\App\DeploymentConfig;

/**
 * Provides basic logic for hashing passwords and encrypting/decrypting misc data
 */
class Encryptor implements EncryptorInterface
{
    /**
     * Crypt key
     */
    const PARAM_CRYPT_KEY = 'crypt/key';

    /**
     * Default length of salt in bytes
     */
    const DEFAULT_SALT_LENGTH = 32;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $_randomGenerator;

    /**
     * Cryptographic key
     *
     * @var string
     */
    protected $_cryptKey;

    /**
     * @var \Magento\Framework\Encryption\CryptFactory
     */
    protected $_cryptFactory;

    /**
     * @var \Magento\Framework\Encryption\Crypt
     */
    protected $_crypt;

    /**
     * @param \Magento\Framework\Math\Random $randomGenerator
     * @param \Magento\Framework\Encryption\CryptFactory $cryptFactory
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        \Magento\Framework\Math\Random $randomGenerator,
        \Magento\Framework\Encryption\CryptFactory $cryptFactory,
        DeploymentConfig $deploymentConfig
    ) {
        $this->_randomGenerator = $randomGenerator;
        $this->_cryptFactory = $cryptFactory;
        $this->_cryptKey = $deploymentConfig->get(self::PARAM_CRYPT_KEY);
    }

    /**
     * Generate a [salted] hash.
     *
     * $salt can be:
     * false - salt is not used
     * true - random salt of the default length will be generated
     * integer - random salt of specified length will be generated
     * string - actual salt value to be used
     *
     * @param string $password
     * @param bool|int|string $salt
     * @return string
     */
    public function getHash($password, $salt = false)
    {
        if ($salt === false) {
            return $this->hash($password);
        }
        if ($salt === true) {
            $salt = self::DEFAULT_SALT_LENGTH;
        }
        if (is_integer($salt)) {
            $salt = $this->_randomGenerator->getRandomString($salt);
        }
        return $this->hash($salt . $password) . ':' . $salt;
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
     * @param string|null $key NULL value means usage of the default key specified on constructor
     * @return \Magento\Framework\Encryption\Crypt
     */
    public function validateKey($key)
    {
        return $this->_getCrypt($key);
    }

    /**
     * Instantiate crypt model
     *
     * @param string|null $key NULL value means usage of the default key specified on constructor
     * @return \Magento\Framework\Encryption\Crypt
     */
    protected function _getCrypt($key = null)
    {
        if ($key === null) {
            if (!$this->_crypt) {
                $this->_crypt = $this->_cryptFactory->create(['key' => $this->_cryptKey]);
            }
            return $this->_crypt;
        } else {
            return $this->_cryptFactory->create(['key' => $key]);
        }
    }
}
