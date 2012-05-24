<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Image file uploader. Could be used for image upload through API
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_Uploader_Image extends Mage_Api2_Model_Request_Uploader_File
{
    /**
     * List of MIME types allowed for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpeg',
        'image/gif' => 'gif',
        'image/png' => 'png'
    );

    /**
     * File name in case if it was not specified in file data
     *
     * @var string
     */
    protected $_defaultFileName = 'image';

    /**
     * Create temporary image file on server using $fileData. Perform additional image validation
     *
     * @param array $fileData
     * @return string Path on server to uploaded temporary image
     */
    public function upload($fileData)
    {
        parent::upload($fileData);
        $this->_validateCreatedImage();
        return $this->_uploadedFilePath;
    }

    /**
     * Check if created image is valid
     *
     * @throws Mage_Api2_Exception
     */
    protected function _validateCreatedImage()
    {
        try {
            // try to create Image object to check if image data is valid
            new Varien_Image($this->_uploadedFilePath);
        } catch (Exception $e) {
            $this->_filesystemAdapter->rmdir($this->_uploadedFilePath, true);
            throw new Mage_Api2_Exception("File content is not an image file.",
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }
}
