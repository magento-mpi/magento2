<?php
/**
 * Archive Extractor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Import_Image_Archive_Extractor
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Adapter_Zip
     */
    protected $_adapter;

    /**
     * @var Magento_Data_Collection_FilesystemFactory
     */
    protected $_collectionFilesystemFactory;

    /**
     * @var Saas_ImportExport_Model_Import_Image_FileSystem
     */
    protected $_fileSystem;

    /**
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Saas_ImportExport_Model_Import_Image_Archive_Adapter_Zip $adapter
     * @param Magento_Data_Collection_FilesystemFactory $collectionFilesystemFactory
     * @param Saas_ImportExport_Model_Import_Image_FileSystem $fileSystem
     */
    public function __construct(
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Saas_ImportExport_Model_Import_Image_Archive_Adapter_Zip $adapter,
        Magento_Data_Collection_FilesystemFactory $collectionFilesystemFactory,
        Saas_ImportExport_Model_Import_Image_FileSystem $fileSystem
    ) {
        $this->_configuration = $configuration;
        $this->_adapter = $adapter;
        $this->_collectionFilesystemFactory = $collectionFilesystemFactory;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * Extract archive
     *
     * @param string $path
     * @param bool $deleteArchiveAfterExtract
     * @return Saas_ImportExport_Model_Import_Image_Archive_Extractor
     * @throws RuntimeException Unsupported file type! Only ZIP file archives allowed
     * @throws RuntimeException Error while extracting images
     */
    public function extract($path, $deleteArchiveAfterExtract = true)
    {
        if (false === $this->_adapter->open($path)) {
            throw new RuntimeException('Unsupported file type! Only ZIP file archives allowed.');
        }

        $result = $this->_adapter->extractTo($this->_configuration->getWorkingUnZipDir());
        $this->_adapter->close();

        if (!$result) {
            throw new RuntimeException('Error while extracting images.');
        }

        if ($deleteArchiveAfterExtract) {
            $this->_fileSystem->removeFile($path);
        }
        return $this;
    }

    /**
     * Get files
     *
     * @return array
     */
    public function getFiles()
    {
        /** @var Magento_Data_Collection_Filesystem $collection */
        $collection = $this->_collectionFilesystemFactory->create();
        $collection->addTargetDir($this->_configuration->getWorkingUnZipDir())
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setFilesFilter(false)
            ->setCollectRecursively(true);

        $data = $collection->toArray();
        return $data['items'];
    }
}
