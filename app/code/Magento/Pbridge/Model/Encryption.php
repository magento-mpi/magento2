<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model;

use Magento\Framework\Math\Random;
use Magento\Framework\Encryption\CryptFactory;
use Magento\Framework\Encryption\Crypt;

class Encryption extends \Magento\Pci\Model\Encryption
{
    /**
     * Constructor
     *
     * @param Random $randomGenerator
     * @param CryptFactory $cryptFactory
     * @param string $cryptKey
     * @param string $key
     */
    public function __construct(Random $randomGenerator, CryptFactory $cryptFactory, $cryptKey, $key)
    {
        parent::__construct($randomGenerator, $cryptFactory, $cryptKey);
        $this->_keys = array($key);
        $this->_keyVersion = 0;
    }

    /**
     * Initialize crypt module if needed
     *
     * By default initializes with latest key and crypt versions
     *
     * @param string $key
     * @param int $cipherVersion
     * @param bool $initVector
     * @return Crypt
     */
    protected function _getCrypt($key = null, $cipherVersion = null, $initVector = true)
    {
        if (null === $key && null == $cipherVersion) {
            $cipherVersion = self::CIPHER_RIJNDAEL_256;
        }

        if (null === $key) {
            $key = $this->_keys[$this->_keyVersion];
        }
        if (null === $cipherVersion) {
            $cipherVersion = $this->_cipher;
        }
        $cipherVersion = $this->validateCipher($cipherVersion);

        if ($cipherVersion === self::CIPHER_RIJNDAEL_128) {
            $cipher = MCRYPT_RIJNDAEL_128;
            $mode = MCRYPT_MODE_ECB;
        } elseif ($cipherVersion === self::CIPHER_RIJNDAEL_256) {
            $cipher = MCRYPT_RIJNDAEL_128;
            $mode = MCRYPT_MODE_CBC;
        } else {
            $cipher = MCRYPT_BLOWFISH;
            $mode = MCRYPT_MODE_ECB;
        }

        return new Crypt($key, $cipher, $mode, $initVector);
    }

    /**
     * Look for key and crypt versions in encrypted data before decrypting
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return parent::decrypt($this->_keyVersion . ':' . self::CIPHER_LATEST . ':' . $data);
    }

    /**
     * Prepend IV to encrypted data after encrypting
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $crypt = $this->_getCrypt();
        return $crypt->getInitVector() . ':' . base64_encode($crypt->encrypt((string)$data));
    }
}
