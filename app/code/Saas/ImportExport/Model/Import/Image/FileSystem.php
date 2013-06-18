<?php
/**
 * FileSystem service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_FileSystem
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * @var Magento_Filesystem
     */
    protected $_fileSystem;

    /**
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Magento_Filesystem $fileSystem
     */
    public function __construct(
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Magento_Filesystem $fileSystem
    ) {
        $this->_configuration = $configuration;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * Move file to media
     *
     * @param string $file
     */
    public function moveFileToMedia($file)
    {
        $mediaDir = $this->_configuration->getMediaDir();
        $this->_fileSystem->setIsAllowCreateDirectories(true)->ensureDirectoryExists($mediaDir);

        $relativePath = str_replace($this->_configuration->getWorkingUnZipDir(), '', $file);
        $this->_fileSystem->rename($file, $mediaDir . $relativePath);
    }

    /**
     * Remove file
     *
     * @param string $file
     */
    public function removeFile($file)
    {
        $this->_fileSystem->delete($file);
    }
}
