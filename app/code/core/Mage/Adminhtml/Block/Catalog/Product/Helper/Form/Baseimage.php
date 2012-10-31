<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form image field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Baseimage extends Varien_Data_Form_Element_Hidden
{
    /**
     * Maximum file size to upload in bytes.
     *
     * @var int
     */
    protected $_maxFileSize;

    /**
     * Media Uploader instance
     *
     * @var Mage_Adminhtml_Block_Media_Uploader
     */
    protected $_mediaUploader;

    /**
     * Model Url instance
     *
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_url;

    /**
     * Media Config instance
     *
     * @var Mage_Catalog_Model_Product_Media_Config
     */
    protected $_mediaConfig;

    /**
     * Design Package instance
     *
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * Data instance
     *
     * @var Mage_Core_Helper_Data
     */
    protected $_helperData;

    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->_mediaUploader = isset($attributes['mediaUploader']) ? $attributes['mediaUploader']
            : Mage::getSingleton('Mage_Adminhtml_Block_Media_Uploader');
        $this->_url = isset($attributes['url']) ? $attributes['url']
            : Mage::getModel('Mage_Adminhtml_Model_Url');
        $this->_mediaConfig = isset($attributes['mediaConfig']) ? $attributes['mediaConfig']
            : Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config');
        $this->_design = isset($attributes['design']) ? $attributes['design']
            : Mage::getSingleton('Mage_Core_Model_Design_Package');
        $this->_helperData = isset($attributes['helperData']) ? $attributes['helperData']
            : Mage::helper('Mage_Core_Helper_Data');

        $this->_maxFileSize = $this->_getFileMaxSize();
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $imageUrl = $this->_helperData->escapeHtml($this->_getImageUrl($this->getValue()));
        $htmlId = $this->_helperData->escapeHtml($this->getHtmlId());
        $uploadUrl = $this->_helperData->escapeHtml($this->_getUploadUrl());

        $html = '<input id="' . $htmlId .'_upload" type="file" name="image" '
                 . 'data-url="' . $uploadUrl . '" style="display: none;" />'
                 . parent::getElementHtml()
                 . '<img align="left" src="' . $imageUrl . '" id="' . $htmlId . '_image"'
                 . ' title="' . $imageUrl . '" alt="' . $imageUrl . '" class="base-image-uploader"'
                 . ' onclick="jQuery(\'#' . $htmlId . '_upload\').trigger(\'click\')"/>';
        $html .= $this->_getJs();

        return $html;
    }

    /**
     * Get js for image uploader
     *
     * @return string
     */
    protected function _getJs()
    {
        return "<script>/* <![CDATA[ */"
               . "jQuery(function(){"
               . "BaseImageUploader({$this->_helperData->jsonEncode($this->getHtmlId())}, "
               . "{$this->_helperData->jsonEncode($this->_maxFileSize)});"
               . " });"
               . "/*]]>*/</script>";
    }

    /**
     * Get full url for image
     *
     * @param string $imagePath
     *
     * @return string
     */
    protected function _getImageUrl($imagePath)
    {
        if (!in_array($imagePath, array(null, 'no_selection', '/'))) {
            if (pathinfo($imagePath, PATHINFO_EXTENSION) == 'tmp') {
                $imageUrl = $this->_mediaConfig->getTmpMediaUrl(substr($imagePath, 0, -4));
            } else {
                $imageUrl = $this->_mediaConfig->getMediaUrl($imagePath);
            }
        } else {
            $imageUrl = $this->_design->getSkinUrl('Mage_Adminhtml::images/image-placeholder.png');
        }

        return $imageUrl;
    }

    /**
     * Get url to upload files
     *
     * @return string
     */
    protected function _getUploadUrl()
    {
        return $this->_url->getUrl('*/catalog_product_gallery/upload');
    }

    /**
     * Get maximum file size to upload in bytes
     *
     * @return int
     */
    protected function _getFileMaxSize()
    {
        return $this->_mediaUploader->getDataMaxSizeInBytes();
    }
}
