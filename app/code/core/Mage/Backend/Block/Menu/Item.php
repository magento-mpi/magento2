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
class Mage_Backend_Block_Menu_Item extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Backend_Block_Menu
     */
    protected $_containerRenderer;
    /**
     * Check whether given item is currently selected
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return bool
     */
    public function isItemActive(Mage_Backend_Model_Menu_Item $item)
    {
        $itemModel = $this->getContainerRenderer()->getActiveItemModel();
        $output = false;

        if ($itemModel instanceof Mage_Backend_Model_Menu_Item &&
            ($itemModel->getId() == $item->getId() || (strpos($itemModel->getFullPath(), $item->getId() . '/') === 0))
        ) {
            $output = true;
        }
        return $output;
    }

    /**
     * Current menu item is last
     * @return bool
     */
    public function isLast()
    {
        return $this->getLevel() == 0 && (int)$this->getContainerRenderer()->getMenuModel()->isLast($this->getItem());
    }

    /**
     * @return Mage_Backend_Block_Menu
     */
    public function getContainerRenderer()
    {
        return $this->_containerRenderer;
    }

    /**
     * @param Mage_Backend_Block_Menu $block
     * @return Mage_Backend_Block_Menu_Item
     */
    public function setContainerRenderer(Mage_Backend_Block_Menu $block)
    {
        $this->_containerRenderer = $block;
        return $this;
    }
}
