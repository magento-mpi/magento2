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
 * Design editor theme list
 *
 * @method array getThemes()
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_List extends Mage_Backend_Block_Template
{
    /**
     * Get list items of themes
     *
     * @param bool $isFeatured
     * @return array
     */
    public function getListItems($isFeatured = true)
    {
        /** @var $itemBlock Mage_DesignEditor_Block_Adminhtml_Theme_Item */
        $itemBlock = $this->getChildBlock('item');

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($this->getThemes() as $theme) {
            if ($isFeatured != $theme->getIsFeatured()) {
                continue;
            }
            $itemBlock->setTheme($theme);
            $items[] = $this->getChildHtml('item', false);
        }

        return $items;
    }
}
