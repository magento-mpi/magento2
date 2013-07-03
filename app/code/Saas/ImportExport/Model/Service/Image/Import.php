<?php
/**
 * Image Import service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Service_Image_Import
{
    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Uploader
     */
    protected $_archiveUploader;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Archive_Extractor
     */
    protected $_archiveExtractor;

    /**
     * @var Magento_Validator_ValidatorInterface
     */
    protected $_imageValidator;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Result
     */
    protected $_importResult;

    /**
     * @var Saas_ImportExport_Model_Import_Image_FileSystem
     */
    protected $_fileSystem;

    /**
     * @param Saas_ImportExport_Model_Import_Image_Archive_Uploader $archiveUploader
     * @param Saas_ImportExport_Model_Import_Image_Archive_Extractor $archiveExtractor
     * @param Saas_ImportExport_Model_Import_Image_Validator_Factory $validatorFactory
     * @param Saas_ImportExport_Model_Import_Image_Result $importResult
     * @param Saas_ImportExport_Model_Import_Image_FileSystem $fileSystem
     */
    public function __construct(
        Saas_ImportExport_Model_Import_Image_Archive_Uploader $archiveUploader,
        Saas_ImportExport_Model_Import_Image_Archive_Extractor $archiveExtractor,
        Saas_ImportExport_Model_Import_Image_Validator_Factory $validatorFactory,
        Saas_ImportExport_Model_Import_Image_Result $importResult,
        Saas_ImportExport_Model_Import_Image_FileSystem $fileSystem
    ) {
        $this->_archiveUploader = $archiveUploader;
        $this->_archiveExtractor = $archiveExtractor;
        $this->_imageValidator = $validatorFactory->createValidator();
        $this->_importResult = $importResult;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * Import process
     *
     * @return Saas_ImportExport_Model_Import_Image_Result
     * @throws Saas_ImportExport_Model_Import_Image_Exception
     */
    public function import()
    {
        try {
            return $this->_import();
        } catch (Exception $e) {
            throw new Saas_ImportExport_Model_Import_Image_Exception($e->getMessage());
        }
    }

    /**
     * Import process (Template method)
     *
     * @return Saas_ImportExport_Model_Import_Image_Result
     */
    protected function _import()
    {
        foreach ($this->_getFiles() as $file) {
            $filename = $file['filename'];
            if ($this->_imageValidator->isValid($filename)) {
                $this->_fileSystem->moveFileToMedia($filename);
                $this->_importResult->addValid($filename);
            } else {
                $this->_fileSystem->removeFile($filename);
                $this->_importResult->addInvalid($filename, $this->_imageValidator->getMessages());
            }
        }
        return $this->_importResult;
    }

    /**
     * @return array
     */
    protected function _getFiles()
    {
        return $this->_archiveExtractor->extract($this->_archiveUploader->upload())->getFiles();
    }
}
