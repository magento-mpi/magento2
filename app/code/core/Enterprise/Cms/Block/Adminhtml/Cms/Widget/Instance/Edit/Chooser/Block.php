<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance block reference chooser
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Block
    extends Mage_Adminhtml_Block_Widget
{
    protected $_layoutHandlesXml = null;

    protected $_layoutHandleUpdates = array();

    protected $_layoutHandleUpdatesXml = null;

    protected $_layoutHandle = array();

    protected $_blocks = array();

    protected $_allowedBlocks = array(
        'core/text_list'
    );

    /**
     * Setter
     *
     * @param array $allowedBlocks
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Block
     */
    public function setAllowedBlocks($allowedBlocks)
    {
        $this->_allowedBlocks = $allowedBlocks;
        return $this;
    }

    /**
     * Add allowed block
     *
     * @param string $block
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Block
     */
    public function addAllowedBlock($block)
    {
        $this->_allowedBlocks[] = $type;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getAllowedBlocks()
    {
        return $this->_allowedBlocks;
    }

    /**
     * Setter
     * If string given exlopde to array by ',' delimiter
     *
     * @param string|array $layoutHandle
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Block
     */
    public function setLayoutHandle($layoutHandle)
    {
        if (is_string($layoutHandle)) {
            $layoutHandle = explode(',', $layoutHandle);
        }
        $this->_layoutHandle = array_merge(array('default'), $layoutHandle);
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

    protected function _toHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName('block')
            ->setClass('required-entry')
            ->setExtraParams('onchange="WidgetInstance.loadSelectBoxByType(\'block_template\', this.up(\'div.group_container\'), this.value)"')
            ->setOptions($this->getBlocks())
            ->setValue($this->getSelected());
        return parent::_toHtml().$selectBlock->toHtml();
    }

    /**
     * Retrieve blocks array
     *
     * @return array
     */
    public function getBlocks()
    {
        if (empty($this->_blocks)) {
            /* @var $update Mage_Core_Model_Layout_Update */
            $update = Mage::getModel('core/layout')->getUpdate();
            /* @var $layoutHandles Mage_Core_Model_Layout_Element */
            $this->_layoutHandlesXml = $update->getFileLayoutUpdatesXml(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme(), Mage::app()->getDefaultStoreView()->getId());
            $this->_collectLayoutHandles();
            $this->_collectBlocks();
            array_unshift($this->_blocks, array(
                'value' => '',
                'label' => Mage::helper('enterprise_cms')->__('-- Please Select --')
            ));
        }
        return $this->_blocks;
    }

    protected function _collectLayoutHandles()
    {
        foreach ($this->getLayoutHandle() as $handle) {
            $this->_mergeLayoutHandles($handle);
        }
        $updatesStr = '<'.'?xml version="1.0"?'.'><layout>'.implode('', $this->_layoutHandleUpdates).'</layout>';
        $this->_layoutHandleUpdatesXml = simplexml_load_string($updatesStr, 'Varien_Simplexml_Element');
    }

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
     *
     * @param Mage_Core_Model_Layout_Element $layoutHandles
     */
    protected function _collectBlocks()
    {
//        foreach ($this->getLayoutHandle() as $handle) {
//            $wildCard = "//{$handle}//block/label/..";
            if ($blocks = $this->_layoutHandleUpdatesXml->xpath('//block/label/..')) {
//            if ($blocks = $this->_layoutHandlesXml->xpath($wildCard)) {
                /* @var $block Mage_Core_Model_Layout_Element */
                foreach ($blocks as $block) {
                    if ((string)$block->getAttribute('name') && $this->_filterBlock($block)) {
                        if ($module = $block->getAttribute('module')) {
                            $helper = Mage::helper($module);
                        } else {
                            $helper = Mage::helper('core');
                        }
                        $this->_blocks[(string)$block->getAttribute('name')] = $helper->__((string)$block->label);
                    }
                }
            }
//        }
        asort($this->_blocks, SORT_STRING);
    }

    /**
     * Check whether given block match allowed block types
     *
     * @param Mage_Core_Model_Layout_Element $block
     * @return boolean
     */
    protected function _filterBlock($block)
    {
        if (!$this->getAllowedBlocks()) {
            return true;
        }
        if (in_array((string)$block->getAttribute('name'), $this->getAllowedBlocks())) {
            return true;
        }
        return false;
    }
}
