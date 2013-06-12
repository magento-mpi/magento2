<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for Mage_Core_Model_Theme_File
 */
class Mage_Core_Model_Theme_FileFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Mage_Core_Model_Theme_File
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Theme_File', $data);
    }
}
