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
 * Design Editor main helper
 */
class Mage_DesignEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check whether block could be draggable
     *
     * @param Mage_Core_Block_Abstract $block
     * @return bool
     */
    public function isBlockDraggable(Mage_Core_Block_Abstract $block)
    {
        $containerBlocks = array('Mage_Core_Block_Text_List', 'Mage_Page_Block_Html_Wrapper');
        if (($parent = $block->getParentBlock()) != null && in_array($parent->getType(), $containerBlocks)) {
            return true;
        }
        return false;
    }
}
