<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files files block
 *
 * @method Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files setStorage(Mage_Theme_Model_Wysiwyg_Storage $storage)
 * @method Mage_Theme_Model_Wysiwyg_Storage getStorage
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Content_Files
    extends Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Files
{
    /**
     * @return string
     */
    public function getStorageType()
    {
        return $this->__($this->helper('Mage_Theme_Helper_Storage')->getStorageType());
    }

}
