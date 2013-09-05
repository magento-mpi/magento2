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
class Magento_Core_Model_Encryption
{
    /**
     * @var \Magento\Crypt
     */
    protected $_crypt;
    /**
     * @var string
     */
    protected $_helper;

    /**
     * @var \Magento\ObjectManager|null
     */
    protected $_objectManager = null;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Set helper instance
     *
     * @param Magento_Core_Helper_Data|string $helper
     * @return Magento_Core_Model_Encryption
     * @throws InvalidArgumentException
     */
    public function setHelper($helper)
    {
        if (!is_string($helper)) {
            if ($helper instanceof Magento_Core_Helper_Abstract) {
                $helper = get_class($helper);
            } else {
                throw new InvalidArgumentException(
                    'Input parameter "$helper" must be either "string" or instance of "Magento_Core_Helper_Abstract"'
                );
            }
        }
        $this->_helper = $helper;
        return $this;
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
            $salt = $this->_objectManager->get($this->_helper)->getRandomString($salt);
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
     * @return bool
     * @throws Exception
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
        Mage::throwException('Invalid hash.');
    }

    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return \Magento\Crypt
     */
    protected function _getCrypt($key = null)
    {
        if (!$this->_crypt) {
            if (null === $key) {
                $key = (string)Mage::getConfig()->getNode('global/crypt/key');
            }
            $this->_crypt = new \Magento\Crypt($key);
        }
        return $this->_crypt;
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
}
