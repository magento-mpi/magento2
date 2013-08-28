<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files files block
 *
 * @method Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files
 *    setStorage(Magento_Theme_Model_Wysiwyg_Storage $storage)
 * @method Magento_Theme_Model_Wysiwyg_Storage getStorage
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Content_Files
    extends Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files
{
    /**
     * Theme storage
     *
     * @var Magento_Theme_Helper_Storage
     */
    protected $_themeStorage = null;

    /**
     * @param Magento_Theme_Helper_Storage $themeStorage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Theme_Helper_Storage $themeStorage,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_themeStorage = $themeStorage;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return string
     */
    public function getStorageType()
    {
        return __($this->_themeStorage->getStorageType());
    }

}
