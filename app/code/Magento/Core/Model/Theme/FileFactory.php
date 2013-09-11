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
 * Factory for \Magento\Core\Model\Theme\File
 */
namespace Magento\Core\Model\Theme;

class FileFactory
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
     * @return \Magento\Core\Model\Theme\File
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Core\Model\Theme\File', $data);
    }
}
