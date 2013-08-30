<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Top menu block
 *
 * @category    Magento
 * @package     Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Topmenu extends Magento_Core_Block_Template
{
    /**
     * Top menu data tree
     *
     * @var Magento_Data_Tree_Node
     */
    protected $_menu;

    /**
     * Init top menu tree structure
     */
    public function _construct()
    {
        $this->_menu = new Magento_Data_Tree_Node(array(), 'root', new Magento_Data_Tree());

        // enabling the cache for this topmenu to not expire until changes made in admin area
        // this is to prevent the menu from being rebuild every request and to prevent new categories from showing up
        // immediately
        $this->addData(array(
            'cache_lifetime'    => false,
            'cache_tags'        => array(
                Magento_Core_Model_Store_Group::CACHE_TAG
            ),
        ));
    }

    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch('page_block_html_topmenu_gethtml_before', array(
            'menu' => $this->_menu
        ));

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        $transportObject = new Magento_Object(array('html' => $html));
        $this->_eventManager->dispatch('page_block_html_topmenu_gethtml_after', array(
            'menu'            => $this->_menu,
            'transportObject' => $transportObject,
        ));

        return $html;
    }

    /**
     * Count All Subnavigation Items
     *
     * @param Magento_Backend_Model_Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = $items->count();
        foreach ($items as $item) {
            /** @var $item Magento_Backend_Model_Menu_Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param Magento_Backend_Model_Menu $items
     * @param int $limit
     * @return array
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);

        if ($total <= $limit) {
            return;
        }
        $result[] = array(
                'total' => $total,
                'max'   => (int)ceil($total / ceil($total / $limit))
            );

        $count = 0;
        $firstCol = true;
        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;
            if ($place >= $limit) {
                $colbrake = !$firstCol;
                $count = 0;
            } elseif ($count >= $limit) {
                $colbrake = !$firstCol;
                $count = $place;
            } else {
                $colbrake = false;
            }
            $result[] = array(
                'place' => $place,
                'colbrake' => $colbrake
            );
            $firstCol = false;
        }
        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param $menuItem Magento_Backend_Model_Menu_Item
     * @param $level int
     * @param $limit int
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }
        if (!empty($childrenWrapClass)) {
            $html .= '<div class="' . $childrenWrapClass . '">';
        }
        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }
        $html .= '<ul class="level' . $childLevel . '">';
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        if (!empty($childrenWrapClass)) {
            $html .= '</div>';
        }
        return $html;
    }


    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Magento_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Magento_Data_Tree_Node $menuTree, $childrenWrapClass, $limit, $colBrakes = array())
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>'
                . $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
                . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param Magento_Data_Tree_Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(Magento_Data_Tree_Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);

        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }

        return $html;
    }

    /**
     * Returns array of menu item's attributes
     *
     * @param Magento_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(Magento_Data_Tree_Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);
        $attributes = array(
            'class' => implode(' ', $menuItemClasses)
        );

        return $attributes;
    }

    /**
     * Returns array of menu item's classes
     *
     * @param Magento_Data_Tree_Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Magento_Data_Tree_Node $item)
    {
        $classes = array();

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }
}
