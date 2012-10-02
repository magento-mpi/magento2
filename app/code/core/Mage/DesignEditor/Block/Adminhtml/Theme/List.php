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
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_List extends Mage_Backend_Block_Widget_Container
{
    /**
     * So called "container controller" to specify group of blocks participating in some action
     *
     * @var string
     */
    protected $_controller = 'vde';

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Themes List');
    }

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

        /** @var $model Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            if ($isFeatured != $theme->getIsFeatured()) {
                continue;
            }
            $itemBlock->setTheme($theme);
            $items[] = $this->getChildHtml('item', false);
        }

        return $items;
    }
}
