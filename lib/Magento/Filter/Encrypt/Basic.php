<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter\Encrypt;

class Basic implements \Zend_Filter_Encrypt_Interface
{
    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(\Magento\Encryption\EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * Encrypt value
     *
     * @param string $value
     * @return string
     */
    public function encrypt($value)
    {
        return $this->encryptor->encrypt($value);
    }

    /**
     * Decrypt value
     *
     * @param string $value
     * @return string
     */
    public function decrypt($value)
    {
        return $this->encryptor->encrypt($value);
    }
}
