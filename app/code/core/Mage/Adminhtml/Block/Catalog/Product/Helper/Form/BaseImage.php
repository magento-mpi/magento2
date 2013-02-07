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
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_BaseImage extends Varien_Data_Form_Element_Abstract
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
     * @var Mage_Backend_Model_Url
     */
    protected $_url;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * @var Mage_Catalog_Helper_Data
     */
    protected $_catalogHelperData;

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
            : Mage::getModel('Mage_Backend_Model_Url');
        $this->_coreHelper = isset($attributes['coreHelper']) ? $attributes['coreHelper']
            : Mage::helper('Mage_Core_Helper_Data');
        $this->_catalogHelperData = isset($attributes['catalogHelperData']) ? $attributes['catalogHelperData']
            : Mage::helper('Mage_Catalog_Helper_Data');

        $this->_maxFileSize = $this->_getFileMaxSize();
    }

    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = ($this->getNoSpan() === true) ? '' : '<span class="field-row">' . "\n";
            $html .= $this->getLabelHtml();
            $html .= $this->getElementHtml();
            $html .= ($this->getNoSpan() === true) ? '' : '</span>' . "\n";
        }
        return $html;
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $htmlId = $this->_coreHelper->escapeHtml($this->getHtmlId());
        $uploadUrl = $this->_coreHelper->escapeHtml($this->_getUploadUrl());
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getForm()->getDataObject();
        $gallery = $product->getMediaGalleryImages();
        $html = '<input id="' . $htmlId .'-upload" type="file" name="image" '
            . 'data-url="' . $uploadUrl . '" style="display:none" />'
            . '<input id="' . $htmlId . '" type="hidden" name="'. $this->getName() .'" />'
            . '<div id="' . $htmlId  . '-container" class="images" data-main="' .  $this->getEscapedValue() . '" '
            . 'data-images="' . $this->_coreHelper->escapeHtml(
            $this->_coreHelper->jsonEncode($gallery ? $gallery->toArray() : array())
        ) . '">'
            . '<div class="image image-placeholder" id="' . $htmlId . '-upload-placeholder"><p class="image-placeholder-text">' . $this->helper('Mage_Catalog_Helper_Data')->__('Click here or drag and drop to add images') . '</p></div>'
            . '<script id="' . $htmlId . '-template" type="text/x-jquery-tmpl">'
            . '<div class="image" data-image-label="' . $this->helper('Mage_Catalog_Helper_Data')->__('Main') . '">'
                . '<img class="base-image-uploader" src="${url}" data-position="${position}" alt="${label}" />'
                . '<div class="actions">'
                    . '<button type="button" class="action-delete" title="' . $this->helper('Mage_Catalog_Helper_Data')->__('Delete image') . '">'
                        . '<span>' . $this->helper('Mage_Catalog_Helper_Data')->__('Delete image') . '</span>'
                    . '</button>'
                    . '<button type="button" class="action-make-main" title="' . $this->helper('Mage_Catalog_Helper_Data')->__('Make Main') . '">'
                        . '<span>' . $this->helper('Mage_Catalog_Helper_Data')->__('Make Main') . '</span>'
                    . '</button>'
                    . '<div class="draggable-handle"></div>'
                . '</div>'
            . '</div>'
            . '</script>'
            . '</div>';
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
            . "BaseImageUploader({$this->_coreHelper->jsonEncode($this->getHtmlId())}, "
            . "{$this->_coreHelper->jsonEncode($this->_maxFileSize)});"
            . " });"
            . "/*]]>*/</script>";
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

    /**
     * Dummy function to give translation tool the ability to pick messages
     * Must be called with Mage_Catalog_Helper_Data $className only
     *
     * @param string $className
     * @return Mage_Catalog_Helper_Data|Mage_Core_Helper_Data
     */
    private function helper($className)
    {
        return $className === 'Mage_Catalog_Helper_Data' ? $this->_catalogHelperData : $this->_coreHelper;
    }
}
