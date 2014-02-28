<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\File;

/**
 * Factory class for \Magento\Less\PreProcessor\File\FileList
 */
class FileListFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Less\PreProcessor\File\FileList'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return FileList
     * @throws \UnexpectedValueException
     */
    public function create(array $data = array())
    {
        $fileList = $this->objectManager->create($this->instanceName, $data);
        if (!$fileList instanceof FileList) {
            throw new \UnexpectedValueException(
                get_class($fileList) . ' doesn\'t extend \Magento\Less\PreProcessor\File\FileList'
            );
        }
        return $fileList;
    }
}
