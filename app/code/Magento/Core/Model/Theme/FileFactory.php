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
 * Factory for Magento_Core_Model_Theme_File
 */
class Magento_Core_Model_Theme_FileFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento_Core_Model_Theme_File
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento_Core_Model_Theme_File', $data);
    }
}
