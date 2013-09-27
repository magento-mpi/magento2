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
 * Uploader block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Uploader extends Magento_Adminhtml_Block_Media_Uploader
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    protected $_imagesStorage;

    /**
     * @param Magento_Cms_Model_Wysiwyg_Images_Storage $imagesStorage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_File_Size $fileSize
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_Wysiwyg_Images_Storage $imagesStorage,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_File_Size $fileSize,
        array $data = array()
    ) {
        $this->_imagesStorage = $imagesStorage;
        parent::__construct($coreData, $context, $viewUrl, $fileSize, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $type = $this->_getMediaType();
        $allowed = $this->_imagesStorage->getAllowedExtensions($type);
        $labels = array();
        $files = array();
        foreach ($allowed as $ext) {
            $labels[] = '.' . $ext;
            $files[] = '*.' . $ext;
        }
        $this->getConfig()
            ->setUrl($this->_urlBuilder->addSessionParam()->getUrl('*/*/upload', array('type' => $type)))
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => __('Images (%1)', implode(', ', $labels)),
                    'files' => $files
                )
            ));
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
