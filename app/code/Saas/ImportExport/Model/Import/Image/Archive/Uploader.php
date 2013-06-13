<?php
/**
 * Archive Uploader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_Archive_Uploader
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * @var Varien_File_UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Varien_File_UploaderFactory $uploaderFactory
     */
    public function __construct(
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Varien_File_UploaderFactory $uploaderFactory
    ) {
        $this->_configuration = $configuration;
        $this->_uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload archive
     *
     * @return string
     * @throws RuntimeException Image Archive upload error.
     */
    public function upload()
    {
        /** @var Varien_File_Uploader $uploader */
        $uploader = $this->_uploaderFactory->create(array(
            'fileId' => $this->_configuration->getFileFieldName(),
        ));
        $uploader->setAllowedExtensions($this->_configuration->getArchiveAllowedExtensions());
        $result = $uploader->save($this->_configuration->getWorkingDir());

        if (!$result) {
            throw new RuntimeException('Image Archive upload error.');
        }

        return $result['path'] . $result['file'];
    }
}
