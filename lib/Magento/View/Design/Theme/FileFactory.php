<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme;

/**
 * Theme File Factory
 */
class FileFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Object manager
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
     * @return \Magento\View\Design\Theme\FileInterface
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\View\Design\Theme\FileInterface', $data);
    }
}
