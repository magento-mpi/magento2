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
     * Initialize template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mage_Backend::menu/container.phtml');
    }
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
        $block = $this->getLayout()->createBlock('Mage_Backend_Block_Menu_Item');
        $block->setItem($menu);
        $block->setLevel($this->getLevel());
        $block->setContainerRenderer($this->getContainer());
        return $block->toHtml();
    }
}
