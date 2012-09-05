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
 * Page handles navigation control
 */
class Mage_DesignEditor_Block_Toolbar_HandlesHierarchy extends Mage_Core_Block_Template
{
    /**
     * Page handle currently selected
     *
     * @var string|bool
     */
    protected $_selectedHandle;

    /**
     * Recursively render each level of the page handles hierarchy as an HTML list
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
            $class = $info['type'] == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT
                ? ' class="vde_option_fragment"'
                : '';
            $result .= '<li rel="' . $name . '"' . $class . '>';
            $result .= '<a href="' . $this->getUrl('design/editor/page', array('handle' => $name)) . '">';
            $result .= $this->escapeHtml($info['label']);
            $result .= '</a>';
            $result .= $this->_renderHierarchy($info['children']);
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Render page handles hierarchy as an HTML list
     *
     * @return string
     */
    public function renderHierarchy()
    {
        return $this->_renderHierarchy($this->getLayout()->getUpdate()->getPageHandlesHierarchy());
    }

    /**
     * Retrieve the name of the currently selected page handle
     *
     * @return string|false
     */
    public function getSelectedHandle()
    {
        if ($this->_selectedHandle === null) {
            $this->_selectedHandle = false;
            $layoutUpdate = $this->getLayout()->getUpdate();
            $pageHandles = $layoutUpdate->getPageHandles();
            if ($pageHandles) {
                $this->_selectedHandle = end($pageHandles);
            } else {
                foreach (array_reverse($layoutUpdate->getHandles()) as $handle) {
                    if ($layoutUpdate->pageHandleExists($handle)) {
                        $this->_selectedHandle = $handle;
                        break;
                    }
                }
            }
        }
        return $this->_selectedHandle;
    }

    /**
     * Retrieve label for the currently selected page handle
     *
     * @return string|false
     */
    public function getSelectedHandleLabel()
    {
        return $this->escapeHtml($this->getLayout()->getUpdate()->getPageHandleLabel($this->getSelectedHandle()));
    }

    /**
     * Set the name of the currently selected page handle
     *
     * @param string $handleName Page handle name
     */
    public function setSelectedHandle($handleName)
    {
        $this->_selectedHandle = $handleName;
    }
}
