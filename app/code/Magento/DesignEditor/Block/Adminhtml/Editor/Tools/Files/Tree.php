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
    public function __construct(Magento_Theme_Helper_Storage $themeStorage, Magento_Core_Helper_Data $coreData, Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($themeStorage, $coreData, $context, $data);
    }

    /**
     * Override root node name of tree specific to design editor.
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return __('CSS Editor ') . __($this->_themeStorage->getStorageTypeName());
    }
}
