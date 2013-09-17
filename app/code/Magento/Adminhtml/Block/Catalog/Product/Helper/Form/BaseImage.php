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
 * Product form image field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_BaseImage extends Magento_Data_Form_Element_Abstract
{
    /**
     * Model Url instance
     *
     * @var Magento_Backend_Model_Url
     */
    protected $_url;

    /**
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogHelperData;

    /**
     * @var Magento_File_Size
     */
    protected $_fileConfig;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Core_Model_View_UrlFactory $coreViewUrlFactory
     * @param Magento_Backend_Model_UrlFactory $backendUrlFactory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_File_Size $fileConfig
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_Core_Model_View_UrlFactory $coreViewUrlFactory,
        Magento_Backend_Model_UrlFactory $backendUrlFactory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_File_Size $fileConfig,
        array $attributes = array()
    ) {
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);

        $this->_viewUrl = $coreViewUrlFactory->create();
        $this->_url = $backendUrlFactory->create();
        $this->_catalogHelperData = $catalogData;
        $this->_fileConfig = $fileConfig;
        $this->_maxFileSize = $this->_getFileMaxSize();
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Images');
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $htmlId = $this->_coreData->escapeHtml($this->getHtmlId());
        $uploadUrl = $this->_coreData->escapeHtml($this->_getUploadUrl());
        $spacerImage = $this->_viewUrl->getViewFileUrl('images/spacer.gif');
        $imagePlaceholderText = __('Click here or drag and drop to add images');
        $deleteImageText = __('Delete image');
        $makeBaseText = __('Make Base');
        $hiddenText = __('Hidden');
        $imageManagementText = __('Image Management');
        /** @var $product Magento_Catalog_Model_Product */
        $html = <<<HTML
<div id="{$htmlId}-container" class="images"
    data-mage-init="{baseImage:{}}"
    data-max-file-size="{$this->_getFileMaxSize()}"
    >
    <div class="image image-placeholder">
        <input type="file" name="image" data-url="{$uploadUrl}" multiple="multiple" />
        <img class="spacer" src="{$spacerImage}"/>
        <p class="image-placeholder-text">{$imagePlaceholderText}</p>
    </div>
    <script id="{$htmlId}-template" class="image-template" type="text/x-jquery-tmpl">
        <div class="image">
            <img class="spacer" src="{$spacerImage}"/>
            <img class="product-image" src="\${url}" data-position="\${position}" alt="\${label}" />
            <div class="actions">
                <button class="action-delete" data-role="delete-button" title="{$deleteImageText}">
                    <span>{$deleteImageText}</span>
                </button>
                <button class="action-make-base" data-role="make-base-button" title="{$makeBaseText}">
                    <span>{$makeBaseText}</span>
                </button>
                <div class="draggable-handle"></div>
            </div>
            <div class="image-label"></div>
            <div class="image-fade"><span>{$hiddenText}</span></div>
        </div>
    </script>
</div>
<span class="action-manage-images" data-activate-tab="image-management">
    <span>{$imageManagementText}</span>
</span>
<script>
    (function($) {
        'use strict';

        $('[data-activate-tab=image-management]')
            .on('click.toggleImageManagementTab', function() {
                $('#product_info_tabs_image-management').trigger('click');
            });
    })(window.jQuery);
</script>

HTML;
        return $html;
    }

    /**
     * Get url to upload files
     *
     * @return string
     */
    protected function _getUploadUrl()
    {
        return $this->_url->getUrl('adminhtml/catalog_product_gallery/upload');
    }

    /**
     * Get maximum file size to upload in bytes
     *
     * @return int
     */
    protected function _getFileMaxSize()
    {
        return $this->_fileConfig->getMaxFileSize();
    }
}
