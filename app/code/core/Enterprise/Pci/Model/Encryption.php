<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Pci
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * More sophisticated encryption model, that can:
 * - generate/check hashes of different versions
 * - use different encryption ciphers
 */
class Enterprise_Pci_Model_Encryption extends Mage_Core_Model_Encryption
{
    const HASH_VERSION_MD5    = 0;
    const HASH_VERSION_SHA256 = 1;
    const HASH_VERSION_LATEST = 1;

    const CIPHER_BLOWFISH     = 0;
    const CIPHER_RIJNDAEL_128 = 1;
    const CIPHER_LATEST       = 1;

    protected $_cipher = self::CIPHER_LATEST;
    protected $_crypts = array();

    protected $_keyVersion;
    protected $_keys = array();

    public function __construct()
    {
        // load all possible keys
        $this->_keys = preg_split('/\s+/s', trim((string)Mage::getConfig()->getNode('global/crypt/key')));
        $this->_keyVersion = count($this->_keys) - 1;
    }

    /**
     * Check whether specified cipher version is supported
     *
     * Returns matched supported version or throws exception
     *
     * @param int $version
     * @return int
     * @throws Exception
     */
    public function validateCipher($version)
    {
        $version = (int)$version;
        if (!in_array($version, array(self::CIPHER_BLOWFISH, self::CIPHER_RIJNDAEL_128), true)) {
            Mage::throwException('Not supported cipher version');
        }
        return $version;
    }

    /**
     * Validate hash against all supported versions.
     *
     * Priority is by newer version.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validateHash($password, $hash)
    {
        return $this->validateHashByVersion($password, $hash, self::HASH_VERSION_SHA256)
            || $this->validateHashByVersion($password, $hash, self::HASH_VERSION_MD5);
    }

    /**
     * Hash a string
     *
     * @param string $data
     * @param int $version
     * @return string
     */
    public function hash($data, $version = self::HASH_VERSION_LATEST)
    {
        if (self::HASH_VERSION_MD5 === $version) {
            return md5($data);
        }
        return hash('sha256', $data);
    }

    /**
     * Validate hash by specified version
     *
     * @param string $password
     * @param string $hash
     * @param int $version
     * @return bool
     */
    public function validateHashByVersion($password, $hash, $version = self::HASH_VERSION_LATEST)
    {
        // look for salt
        $hashArr = explode(':', $hash, 2);
        if (1 === count($hashArr)) {
            return $this->hash($password, $version) === $hash;
        }
        list($hash, $salt) = $hashArr;
        return $this->hash($salt . $password, $version) === $hash;
    }

    /**
     * Set cipher to be used for encryption/decryption
     *
     * @param int $version
     * @return Enterprise_Pci_Model_Encryption
     */
//    public function setCipher($version = self::CIPHER_LATEST)
//    {
//        $this->_cipher = $this->validateCipher($version);
//        return $this;
//    }

    /**
     * Attempt to append new key & version
     *
     * @param  $key
     * @return Enterprise_Pci_Model_Encryption
     */
    public function setNewKey($key)
    {
        parent::validateKey($key);
        $this->_keys[] = $key;
        $this->_keyVersion += 1;
        return $this;
    }

    /**
     * Export current keys as string
     *
     * @return string
     */
    public function exportKeys()
    {
        return implode("\n", $this->_keys);
    }

    /**
     * Initialize crypt module if needed
     *
     * By default initializes with latest key and crypt versions
     *
     * @param string $key
     * @return Varien_Crypt_Mcrypt
     */
    protected function _getCrypt($key = null, $cipherVersion = null)
    {
        if (null === $key) {
            $key = $this->_keys[$this->_keyVersion];
        }
        if (null === $cipherVersion) {
            $cipherVersion = $this->_cipher;
        }
        $cipherVersion = $this->validateCipher($cipherVersion);

        if (!isset($this->_crypts[$key][$cipherVersion])) {
            $this->_crypts[$key][$cipherVersion] = Varien_Crypt::factory();
            if ($cipherVersion === self::CIPHER_RIJNDAEL_128) {
                $this->_crypts[$key][$cipherVersion]->setCipher(MCRYPT_RIJNDAEL_128);
            }
            $this->_crypts[$key][$cipherVersion]->init($key);
        }
        return $this->_crypts[$key][$cipherVersion];
    }

    /**
     * Look for key and crypt versions in encrypted data before decrypting
     *
     * Unsupported/unspecified key version silently fallback to the oldest we have
     * Unsupported cipher versions eventually throw exception
     * Unspecified cipher version fallback to the oldest we support
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        if ($data) {
            $parts = explode(':', $data, 3);
            $partsCount = count($parts);
            // specified key, specified crypt
            if (3 === $partsCount) {
                list($keyVersion, $cryptVersion, $data) = $parts;
                $keyVersion   = (int)$keyVersion;
                $cryptVersion = (int)$cryptVersion;
            }
            // no key version = oldest key, specified crypt
            elseif (2 === $partsCount) {
                list($cryptVersion, $data) = $parts;
                $keyVersion   = 0;
                $cryptVersion = (int)$cryptVersion;
            }
            // no key version = oldest key, no crypt version = oldest crypt
            elseif (1 === $partsCount) {
                $keyVersion   = 0;
                $cryptVersion = self::CIPHER_BLOWFISH;
            }
            // not supported format
            else {
                return '';
            }
            // no key for decryption
            if (!isset($this->_keys[$keyVersion])) {
                return '';
            }
            $crypt = $this->_getCrypt($this->_keys[$keyVersion], $cryptVersion);
            return str_replace("\x0", '', trim($crypt->decrypt(base64_decode((string)$data))));
        }
        return '';
    }

    /**
     * Prepend key and cipher versions to encrypted data after encrypting
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return $this->_keyVersion . ':' . $this->_cipher . ':' . parent::encrypt($data);
    }

    /**
     * Validate an encryption key
     *
     * @param string $key
     * @return unknown
     */
    public function validateKey($key)
    {
        if ((false !== strpos($key, '<![CDATA[')) || (false !== strpos($key, ']]>')) || preg_match('/\s/s', $key)) {
            throw new Exception(Mage::helper('enterprise_pci')->__('Encryption key format is invalid.'));
        }
        return parent::validateKey($key);
    }
}
