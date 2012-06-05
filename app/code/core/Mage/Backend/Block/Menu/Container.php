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
     * Get menu item children
     * @return Mage_Backend_Model_Menu
     */
    public function getMenuItems()
    {
        return $this->getItem()->getChildren();
    }

    /**
     * Render menu item element
     * @param $menu
     * @return string
     */
    public function renderMenuItem($menu)
    {
        /**
         * Save current level
         */
        $currentLevel = $this->getLevel();

        /**
         * Render child blocks
         */
        $block = $this->getLayout()->getBlock($this->getContainer()->getItemRendererBlock());
        $block->setItem($menu);
        $block->setLevel($currentLevel);
        $block->setContainerRenderer($this->getContainer());
        $output = $block->toHtml();

        /**
         * Set current level, because it will be changed in child block
         */
        $this->setLevel($currentLevel);
        return $output;
    }
}
