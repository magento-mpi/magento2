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
 * \Directory contents block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms\Wysiwyg\Images\Content;

class Files extends \Magento\Adminhtml\Block\Template
{
    /**
     * Files collection object
     *
     * @var \Magento\Data\Collection\Filesystem
     */
    protected $_filesCollection;

    /**
     * Cms wysiwyg images
     *
     * @var \Magento\Cms\Helper\Wysiwyg\Images
     */
    protected $_cmsWysiwygImages = null;

    /**
     * @param \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_cmsWysiwygImages = $cmsWysiwygImages;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepared Files collection for current directory
     *
     * @return \Magento\Data\Collection\Filesystem
     */
    public function getFiles()
    {
        if (! $this->_filesCollection) {
            $this->_filesCollection = \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Images\Storage')
                ->getFilesCollection(
                    $this->_cmsWysiwygImages->getCurrentPath(), $this->_getMediaType()
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
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileId(\Magento\Object $file)
    {
        return $file->getId();
    }

    /**
     * File thumb URL getter
     *
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileThumbUrl(\Magento\Object $file)
    {
        return $file->getThumbUrl();
    }

    /**
     * File name URL getter
     *
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileName(\Magento\Object $file)
    {
        return $file->getName();
    }

    /**
     * Image file width getter
     *
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileWidth(\Magento\Object $file)
    {
        return $file->getWidth();
    }

    /**
     * Image file height getter
     *
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileHeight(\Magento\Object $file)
    {
        return $file->getHeight();
    }

    /**
     * File short name getter
     *
     * @param  \Magento\Object $file
     * @return string
     */
    public function getFileShortName(\Magento\Object $file)
    {
        return $file->getShortName();
    }

    public function getImagesWidth()
    {
        return \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Images\Storage')->getConfigData('resize_width');
    }

    public function getImagesHeight()
    {
        return \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Images\Storage')->getConfigData('resize_height');
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
