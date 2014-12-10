<?php
/**
 * Crypt object factory.
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Encryption;

/**
 * Crypt factory
 */
class CryptFactory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new magento crypt instance
     *
     * @param array $data
     * @return \Magento\Framework\Encryption\Crypt
     */
    public function create($data = [])
    {
        return $this->_objectManager->create('Magento\Framework\Encryption\Crypt', $data);
    }
}
