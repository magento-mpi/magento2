<?php
/**
 * Import Image Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Import_Image_Configuration extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Config xml paths
     */
    const XML_PATH_TYPE_CODE = 'global/importexport/import/image/type_code';
    const XML_PATH_FIELD_ARCHIVE_FILE_NAME = 'global/importexport/import/image/archive/file_field_name';
    const XML_PATH_ARCHIVE_ALLOWED_EXTENSIONS = 'global/importexport/import/image/archive/allowed_extensions';
    const XML_PATH_IMAGE_FILENAME_LIMIT = 'global/importexport/import/image/filename_limit';
    const XML_PATH_IMAGE_ALLOWED_EXTENSIONS = 'global/importexport/import/image/allowed_extensions';
    const XML_PATH_IMAGE_FILE_SIZE_LIMIT = 'global/importexport/import/image/file_size_limit';
    const XML_PATH_IMAGE_ALLOWED_MIMETYPES = 'global/importexport/import/image/allowed_mimetypes';
    const XML_PATH_IMAGE_WIDTH_LIMIT = 'global/importexport/import/image/width_limit';
    const XML_PATH_IMAGE_HEIGHT_LIMIT = 'global/importexport/import/image/height_limit';
    /**#@-*/

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Dir $dir
    ) {
        parent::__construct($context);

        $this->_config = $config;
        $this->_dir = $dir;
    }

    /**
     * Get type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return (string)$this->_config->getNode(self::XML_PATH_TYPE_CODE);
    }

    /**
     * Import/Export working directory (source files, result files, lock files etc)
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->_dir->getDir('var') . DS . 'importexport' . DS . 'images' . DS;
    }

    /**
     * Import product images working directory
     *
     * @return string
     */
    public function getWorkingUnZipDir()
    {
        return $this->getWorkingDir() . 'unzip' . DS;
    }

    /**
     * Get media dir
     *
     * @return string
     */
    public function getMediaDir()
    {
        return $this->_dir->getDir('media') . DS . 'catalog' . DS . 'product' . DS;
    }

    /**
     * Get file field name
     *
     * @return string
     */
    public function getFileFieldName()
    {
        return (string)$this->_config->getNode(self::XML_PATH_FIELD_ARCHIVE_FILE_NAME);
    }

    /**
     * Get allowed extensions for archive
     *
     * @return array
     */
    public function getArchiveAllowedExtensions()
    {
        return array_keys($this->_config->getNode(self::XML_PATH_ARCHIVE_ALLOWED_EXTENSIONS)->asArray());
    }

    /**
     * Gets maximum allowed length of file name
     *
     * @return int
     */
    public function getImageFilenameLimit()
    {
        return (int)$this->_config->getNode(self::XML_PATH_IMAGE_FILENAME_LIMIT);
    }

    /**
     * Get allowed extensions for image
     *
     * @return array
     */
    public function getImageAllowedExtensions()
    {
        return array_keys($this->_config->getNode(self::XML_PATH_IMAGE_ALLOWED_EXTENSIONS)->asArray());
    }

    /**
     * Get image size limit
     *
     * @return int
     */
    public function getImageFileSizeLimit()
    {
        return (int)$this->_config->getNode(self::XML_PATH_IMAGE_FILE_SIZE_LIMIT);
    }

    /**
     * Get allowed mimetypes for image
     *
     * @return array
     */
    public function getImageAllowedMimetypes()
    {
        return array_values($this->_config->getNode(self::XML_PATH_IMAGE_ALLOWED_MIMETYPES)->asArray());
    }

    /**
     * Get image width limit
     *
     * @return int
     */
    public function getImageWidthLimit()
    {
        return (int)$this->_config->getNode(self::XML_PATH_IMAGE_WIDTH_LIMIT);
    }

    /**
     * Get image height limit
     *
     * @return int
     */
    public function getImageHeightLimit()
    {
        return (int)$this->_config->getNode(self::XML_PATH_IMAGE_HEIGHT_LIMIT);
    }
}
