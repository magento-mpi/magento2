<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\PreProcessor\File;

/**
 * Factory class for \Magento\Framework\Less\PreProcessor\File\FileList
 */
class FileListFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
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
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\Less\PreProcessor\File\FileList'
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
                get_class($fileList) . ' doesn\'t extend \Magento\Framework\Less\PreProcessor\File\FileList'
            );
        }
        return $fileList;
    }
}
