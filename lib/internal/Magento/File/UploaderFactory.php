<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\File;

class UploaderFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new uploader instance
     *
     * @param array $data
     * @return Uploader
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\File\Uploader', $data);
    }
}