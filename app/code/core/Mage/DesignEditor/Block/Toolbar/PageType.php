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
 * Page types navigation control
 */
class Mage_DesignEditor_Block_Toolbar_PageType extends Mage_Core_Block_Template
{
    /**
     * @var string|false
     */
    protected $_selectedItem;

    /**
     * Recursively render each level of the page types hierarchy as an HTML list
     *
     * @param array $hierarchy
     * @return string
     */
    protected function _renderHierarchy(array $hierarchy)
    {
        if (!$hierarchy) {
            return '';
        }
        $result = '<ul>';
        foreach ($hierarchy as $name => $info) {
            $result .= '<li rel="' . $name . '">';
            $result .= '<a href="' . $this->getUrl('design/editor/page', array('item' => $name)) . '">';
            $result .= $this->escapeHtml($info['label']);
            $result .= '</a>';
            $result .= $this->_renderHierarchy($info['children']);
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Render page types hierarchy as an HTML list
     *
     * @return string
     */
    public function renderHierarchy()
    {
        return $this->_renderHierarchy($this->getLayout()->getUpdate()->getPageTypesHierarchy());
    }

    /**
     * Retrieve the name of the currently selected item
     *
     * @return string|false
     */
    public function getSelectedItem()
    {
        if ($this->_selectedItem === null) {
            $this->_selectedItem = false;
            $layoutUpdate = $this->getLayout()->getUpdate();
            $pageHandles = $layoutUpdate->getPageHandles();
            if ($pageHandles) {
                $this->_selectedItem = end($pageHandles);
            } else {
                foreach (array_reverse($layoutUpdate->getHandles()) as $handle) {
                    if ($layoutUpdate->pageItemExists($handle)) {
                        $this->_selectedItem = $handle;
                        break;
                    }
                }
            }
        }
        return $this->_selectedItem;
    }

    /**
     * Retrieve label for the currently selected item
     *
     * @return string|false
     */
    public function getSelectedItemLabel()
    {
        return $this->escapeHtml($this->getLayout()->getUpdate()->getPageItemLabel($this->getSelectedItem()));
    }

    /**
     * Set the name of the currently selected page type
     *
     * @param string $name Page type name
     */
    public function setSelectedItem($name)
    {
        $this->_selectedItem = $name;
    }
}
