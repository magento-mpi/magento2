<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A chooser for container for widget instances
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container
    extends Mage_Adminhtml_Block_Widget
{
    protected $_layoutHandlesXml = null;

    protected $_layoutHandleUpdates = array();

    protected $_layoutHandleUpdatesXml = null;

    protected $_layoutHandle = array();

    protected $_containers = array();

    protected $_allowedContainers = array();

    /**
     * Set a list of container names to be filtered
     *
     * @param array $names
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container
     */
    public function setAllowedContainers($names)
    {
        $this->_allowedContainers = $names;
        return $this;
    }

    /**
     * Get a list of container names that a widget is restricted to
     *
     * @return array
     */
    public function getAllowedContainers()
    {
        return $this->_allowedContainers;
    }

    /**
     * Setter
     * If string given exlopde to array by ',' delimiter
     *
     * @param string|array $layoutHandle
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container
     */
    public function setLayoutHandle($layoutHandle)
    {
        if (is_string($layoutHandle)) {
            $layoutHandle = explode(',', $layoutHandle);
        }
        $this->_layoutHandle = array_merge(array('default'), (array)$layoutHandle);
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandle()
    {
        return $this->_layoutHandle;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getArea()
    {
        if (!$this->_getData('area')) {
            return Mage_Core_Model_Design_Package::DEFAULT_AREA;
        }
        return $this->_getData('area');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        if (!$this->_getData('package')) {
            return Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
        }
        return $this->_getData('package');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        if (!$this->_getData('theme')) {
            return Mage_Core_Model_Design_Package::DEFAULT_THEME;
        }
        return $this->_getData('theme');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('block')
            ->setClass('required-entry select')
            ->setExtraParams('onchange="WidgetInstance.loadSelectBoxByType(\'block_template\','
                .' this.up(\'div.group_container\'), this.value)"')
            ->setOptions($this->getContainers())
            ->setValue($this->getSelected());
        return parent::_toHtml().$selectBlock->toHtml();
    }

    /**
     * Collect available containers in the system and return them as an array of label-values
     *
     * @return array
     */
    public function getContainers()
    {
        if (empty($this->_containers)) {
            /* @var $update Mage_Core_Model_Layout_Update */
            $update = Mage::getModel('Mage_Core_Model_Layout')->getUpdate();
            /* @var $layoutHandles Mage_Core_Model_Layout_Element */
            $this->_layoutHandlesXml = $update->getFileLayoutUpdatesXml(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme());
            $this->_collectLayoutHandles();
            $this->_collectContainers();
            array_unshift($this->_containers, array(
                'value' => '',
                'label' => Mage::helper('Mage_Widget_Helper_Data')->__('-- Please Select --')
            ));
        }
        return $this->_containers;
    }

    /**
     * Merging layout handles and create xml of merged layout handles
     *
     */
    protected function _collectLayoutHandles()
    {
        foreach ($this->getLayoutHandle() as $handle) {
            $this->_mergeLayoutHandles($handle);
        }
        $updatesStr = '<'.'?xml version="1.0"?'.'><layout>'.implode('', $this->_layoutHandleUpdates).'</layout>';
        $this->_layoutHandleUpdatesXml = simplexml_load_string($updatesStr, 'Varien_Simplexml_Element');
    }

    /**
     * Adding layout handle that specified in node 'update' to general layout handles
     *
     * @param string $handle
     */
    public function _mergeLayoutHandles($handle)
    {
        foreach ($this->_layoutHandlesXml->{$handle} as $updateXml) {
            foreach ($updateXml->children() as $child) {
                if (strtolower($child->getName()) == 'update' && isset($child['handle'])) {
                    $this->_mergeLayoutHandles((string)$child['handle']);
                }
            }
            $this->_layoutHandleUpdates[] = $updateXml->asNiceXml();
        }
    }


    /**
     * Filter and collect blocks into array
     */
    protected function _collectContainers()
    {
        if ($nodes = $this->_layoutHandleUpdatesXml->xpath('//container')) {
            /* @var $node Mage_Core_Model_Layout_Element */
            foreach ($nodes as $node) {
                if (!$this->_allowedContainers || in_array($node->getAttribute('name'), $this->_allowedContainers)) {
                    $helper = Mage::helper(Mage_Core_Model_Layout::findTranslationModuleName($node));
                    $this->_containers[$node->getAttribute('name')] = $helper->__($node->getAttribute('label'));
                }
            }
        }
        asort($this->_containers, SORT_STRING);
    }
}
