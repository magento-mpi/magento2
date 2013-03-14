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
 *
 * @method array getHierarchy() getHierarchy()
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_HandlesHierarchy setHierarchy() setHierarchy(array $data)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_HandlesHierarchy
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract
{
    /**
     * Page handle currently selected
     *
     * @var string
     */
    protected $_selectedHandle;

    /**
     * VDE url model
     *
     * @var Mage_DesignEditor_Model_Url_Handle
     */
    protected $_vdeUrlBuilder;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_DesignEditor_Model_Url_Handle $vdeUrlBuilder
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_DesignEditor_Model_Url_Handle $vdeUrlBuilder,
        array $data = array()
    ) {
        $this->_vdeUrlBuilder = $vdeUrlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Recursively render each level of the page handles hierarchy
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
            $linkUrl = $this->_vdeUrlBuilder->getUrl('design/page/type', array('handle' => $name));
            $class = $info['type'] == Mage_Core_Model_Layout_Merge::TYPE_FRAGMENT
                ? ' class="vde_option_fragment"'
                : '';
            $result .= '<li rel="' . $name . '"' . $class . '>';
            $result .= '<a href="' . $linkUrl. '">';
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
        return $this->_renderHierarchy($this->getHierarchy());
    }

    /**
     * Retrieve the name of the currently selected page handle
     *
     * @return string|null
     */
    public function getSelectedHandle()
    {
        if ($this->_selectedHandle === null) {
            $pageHandles = $this->getHierarchy();
            $defaultHandle = reset($pageHandles);
            if ($defaultHandle !== false) {
                $this->_selectedHandle = $defaultHandle['name'];
            }
        }
        return $this->_selectedHandle;
    }

    /**
     * Retrieve label for the currently selected page handle
     *
     * @return string|null
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
