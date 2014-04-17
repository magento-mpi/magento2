<?php
/**
 * Crypt object factory.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Encryption;

/**
 * Crypt factory
 */
class CryptFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new magento crypt instance
     *
     * @param array $data
     * @return \Magento\Encryption\Crypt
     */
    public function create($data = array())
    {
        return $this->_objectManager->create('Magento\Encryption\Crypt', $data);
    }
}
