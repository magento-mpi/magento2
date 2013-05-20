<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files content block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Content
    extends Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content
{
    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('CSS Editor ') . $this->__($this->helper('Mage_Theme_Helper_Storage')->getStorageTypeName());
    }
}
