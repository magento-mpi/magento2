<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model;

interface EncryptionInterface
{
    /**
     * Set helper instance
     *
     * @param \Magento\Core\Helper\Data|string $helper
     * @return \Magento\Core\Model\Encryption
     * @throws \InvalidArgumentException
     */
    public function setHelper($helper);

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
    public function getHash($password, $salt = false);

    /**
     * Hash a string
     *
     * @param string $data
     * @return string
     */
    public function hash($data);

    /**
     * Validate hash against hashing method (with or without salt)
     *
     * @param string $password
     * @param string $hash
     * @return bool
     * @throws Exception
     */
    public function validateHash($password, $hash);

    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypt a string
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data);

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string $key
     * @return \Magento\Crypt
     */
    public function validateKey($key);
}
