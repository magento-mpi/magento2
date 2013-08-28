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
 * Block class for rendering design editor tree of files
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Tree
    extends Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Tree
{
    /**
     * Override root node name of tree specific to design editor.
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return __('CSS Editor ') . __($this->helper('Magento_Theme_Helper_Storage')->getStorageTypeName());
    }
}
