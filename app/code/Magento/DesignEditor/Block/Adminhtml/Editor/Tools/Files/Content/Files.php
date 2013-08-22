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
     * @return string
     */
    public function getStorageType()
    {
        return __($this->helper('Magento_Theme_Helper_Storage')->getStorageType());
    }

}
