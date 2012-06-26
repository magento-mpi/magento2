<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend menu item block
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Menu_Container extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected $_menu;

    /**
     * Set menu model
     * @return Mage_Backend_Model_Menu
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * Get menu filter iterator
     * @return Mage_Backend_Model_Menu_Filter_Iterator
     */
    public function getMenuIterator()
    {
        return Mage::getModel('Mage_Backend_Model_Menu_Filter_Iterator', $this->getMenu()->getIterator());
    }

    /**
     * Get menu model
     *
     * @param Mage_Backend_Model_Menu $menu
     * @return Mage_Backend_Block_Menu_Container
     */
    public function setMenu(Mage_Backend_Model_Menu $menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Render menu item element
     * @param Mage_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    public function renderMenuItem(Mage_Backend_Model_Menu_Item $menuItem)
    {
        /**
         * Save current level
         */
        $currentLevel = $this->getLevel();

        /**
         * Render child blocks
         * @var Mage_Backend_Block_Menu_Item
         */
        $block = $this->getMenuBlock()->getChildBlock($this->getMenuBlock()->getItemRendererBlock());
        $block->setMenuItem($menuItem);
        $block->setLevel($currentLevel);
        $block->setContainerRenderer($this->getMenuBlock());
        $output = $block->toHtml();

        /**
         * Set current level, because it will be changed in child block
         */
        $this->setLevel($currentLevel);
        return $output;
    }
}
