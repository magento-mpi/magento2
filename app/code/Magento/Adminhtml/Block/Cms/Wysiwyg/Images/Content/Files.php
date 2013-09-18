<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory contents block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Files extends Magento_Adminhtml_Block_Template
{
    /**
     * Files collection object
     *
     * @var Magento_Data_Collection_Filesystem
     */
    protected $_filesCollection;

    /**
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    protected $_imageStorage;

    /**
     * @param Magento_Cms_Model_Wysiwyg_Images_Storage $imageStorage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_Wysiwyg_Images_Storage $imageStorage,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_imageStorage = $imageStorage;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepared Files collection for current directory
     *
     * @return Magento_Data_Collection_Filesystem
     */
    public function getFiles()
    {
        if (! $this->_filesCollection) {
            $this->_filesCollection = $this->_imageStorage->getFilesCollection(
                    Mage::helper('Magento_Cms_Helper_Wysiwyg_Images')->getCurrentPath(), $this->_getMediaType()
                );
        }

        return $this->_filesCollection;
    }

    /**
     * Files collection count getter
     *
     * @return int
     */
    public function getFilesCount()
    {
        return $this->getFiles()->count();
    }

    /**
     * File idetifier getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileId(Magento_Object $file)
    {
        return $file->getId();
    }

    /**
     * File thumb URL getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileThumbUrl(Magento_Object $file)
    {
        return $file->getThumbUrl();
    }

    /**
     * File name URL getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileName(Magento_Object $file)
    {
        return $file->getName();
    }

    /**
     * Image file width getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileWidth(Magento_Object $file)
    {
        return $file->getWidth();
    }

    /**
     * Image file height getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileHeight(Magento_Object $file)
    {
        return $file->getHeight();
    }

    /**
     * File short name getter
     *
     * @param  Magento_Object $file
     * @return string
     */
    public function getFileShortName(Magento_Object $file)
    {
        return $file->getShortName();
    }

    /**
     * Get image width
     *
     * @return int
     */
    public function getImagesWidth()
    {
        return $this->_imageStorage->getResizeWidth();
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function getImagesHeight()
    {
        return $this->_imageStorage->getResizeHeight();
    }

    /**
     * Return current media type based on request or data
     * @return string
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
