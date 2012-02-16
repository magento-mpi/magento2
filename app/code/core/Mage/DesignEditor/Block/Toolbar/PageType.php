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
     * @var string
     */
    protected $_selectedPageType;

    /**
     * Recursively render each level of the page types hierarchy as an HTML list
     *
     * @param array $pageTypes
     * @return string
     */
    protected function _renderPageTypes(array $pageTypes)
    {
        if (!$pageTypes) {
            return '';
        }
        $result = '<ul>';
        foreach ($pageTypes as $name => $info) {
            $result .= '<li rel="' . $name . '">';
            $result .= '<a href="' . $this->getUrl('design/editor/page', array('page_type' => $name)) . '">';
            $result .= $this->escapeHtml($info['label']);
            $result .= '</a>';
            $result .= $this->_renderPageTypes($info['children']);
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
    public function renderPageTypes()
    {
        return $this->_renderPageTypes(
            $this->getLayout()->getPageTypesHierarchy()
        );
    }

    /**
     * Retrieve the name of the currently selected page type
     *
     * @return string
     */
    public function getSelectedPageType()
    {
        if ($this->_selectedPageType === null) {
            $this->_selectedPageType = false;
            $pageTypes = $this->getLayout()->getPageTypesFlat();
            foreach (array_reverse($this->getLayout()->getUpdate()->getPageHandles()) as $pageHandle) {
                if (array_key_exists($pageHandle, $pageTypes)) {
                    $this->_selectedPageType = $pageHandle;
                    break;
                }
            }
        }
        return $this->_selectedPageType;
    }

    /**
     * Set the name of the currently selected page type
     *
     * @param string $name Page type name
     */
    public function setSelectedPageType($name)
    {
        $this->_selectedPageType = $name;
    }
}
