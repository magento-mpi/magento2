<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import entity product model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Uploader extends Mage_Core_Model_File_Uploader
{
    protected $_tmpDir  = '';
    protected $_destDir = '';
    protected $_allowedMimeTypes = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png'
    );
    const DEFAULT_FILE_TYPE = 'application/octet-stream';

    /**
     * @var Mage_Core_Model_Image_AdapterFactory
     */
    protected $_imageFactory;

    function __construct(Mage_Core_Model_Image_AdapterFactory $imageFactory, $filePath = null)
    {
        if (!is_null($filePath)) {
            $this->_setUploadFile($filePath);
        }
        $this->_imageFactory = $imageFactory;
    }

    /**
     * Initiate uploader defoult settings
     */
    public function init()
    {
        $this->setAllowRenameFiles(true);
        $this->setAllowCreateFolders(true);
        $this->setFilesDispersion(true);
        $this->setAllowedExtensions(array_keys($this->_allowedMimeTypes));
        $imageAdapter = $this->_imageFactory->create();
        $this->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
        $this->_uploadType = self::SINGLE_STYLE;
    }

    /**
     * Proceed moving a file from TMP to destination folder
     *
     * @param string $fileName
     * @return array
     */
    public function move($fileName)
    {
        $filePath = realpath($this->getTmpDir() . DS . $fileName);
        $this->_setUploadFile($filePath);
        $result = $this->save($this->getDestDir());
        $result['name'] = self::getCorrectFileName($result['name']);
        return $result;
    }

    /**
     * Prepare information about the file for moving
     *
     * @param string $filePath
     */
    protected function _setUploadFile($filePath)
    {
        if (!is_readable($filePath)) {
            Mage::throwException("File '{$filePath}' was not found or has read restriction.");
        }
        $this->_file = $this->_readFileInfo($filePath);

        $this->_validateFile();
    }

    /**
     * Reads file info
     *
     * @param string $filePath
     * @return array
     */
    protected function _readFileInfo($filePath)
    {
        $fileInfo = pathinfo($filePath);

        return array(
            'name' => $fileInfo['basename'],
            'type' => $this->_getMimeTypeByExt($fileInfo['extension']),
            'tmp_name' => $filePath,
            'error' => 0,
            'size' => filesize($filePath)
        );
    }

    /**
     * Validate uploaded file by type and etc.
     */
    protected function _validateFile()
    {
        $filePath = $this->_file['tmp_name'];
        if (is_readable($filePath)) {
            $this->_fileExists = true;
        } else {
            $this->_fileExists = false;
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!$this->checkAllowedExtension($fileExtension)) {
            throw new Exception('Disallowed file type.');
        }
        //run validate callbacks
        foreach ($this->_validateCallbacks as $params) {
            if (is_object($params['object']) && method_exists($params['object'], $params['method'])) {
                $params['object']->$params['method']($filePath);
            }
        }
    }

    /**
     * Returns file MIME type by extension
     *
     * @param string $ext
     * @return string
     */
    protected function _getMimeTypeByExt($ext)
    {
        if (array_key_exists($ext, $this->_allowedMimeTypes)) {
            return $this->_allowedMimeTypes[$ext];
        }
        return '';
    }

    /**
     * Obtain TMP file path prefix
     *
     * @return string
     */
    public function getTmpDir()
    {
        return $this->_tmpDir;
    }

    /**
     * Set TMP file path prefix
     *
     * @param string $path
     * @return bool
     */
    public function setTmpDir($path)
    {
        if (is_string($path) && is_readable($path)) {
            $this->_tmpDir = $path;
            return true;
        }
        return false;
    }

    /**
     * Obtain destination file path prefix
     *
     * @return string
     */
    public function getDestDir()
    {
        return $this->_destDir;
    }

    /**
     * Set destination file path prefix
     *
     * @param string $path
     * @return bool
     */
    public function setDestDir($path)
    {
        if (is_string($path) && is_writable($path)) {
            $this->_destDir = $path;
            return true;
        }
        return false;
    }

    /**
     * Move files from TMP folder into destination folder
     *
     * @param string $tmpPath
     * @param string $destPath
     * @return bool
     */
    protected function _moveFile($tmpPath, $destPath)
    {
        $sourceFile = realpath($tmpPath);
        if ($sourceFile !== false) {
            return copy($sourceFile, $destPath);
        } else {
            return false;
        }
    }

}
