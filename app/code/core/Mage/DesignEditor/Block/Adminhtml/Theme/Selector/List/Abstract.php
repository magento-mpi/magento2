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
 * Abstract theme list
 *
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Backend_Block_Abstract setCollection(Mage_Core_Model_Resource_Theme_Collection $collection)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
    extends Mage_Backend_Block_Abstract
{
    /**
     * Get tab title
     *
     * @return string
     */
    abstract public function getTabTitle();

    /**
     * Get list items of themes
     *
     * @return array
     */
    public function getListItems()
    {
        /** @var $itemBlock Mage_DesignEditor_Block_Adminhtml_Theme_Item */
        $itemBlock = $this->getChildBlock('item');

        $themeCollection = $this->getCollection();

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $itemBlock->setTheme($theme);
            $items[] = $this->getChildHtml('item', false);
        }

        return $items;
    }
}
