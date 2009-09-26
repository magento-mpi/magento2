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
 * Cms Widget Instance layouts chooser
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Layout
    extends Mage_Adminhtml_Block_Widget
{
    protected $_layoutHandles = array();

    /**
     * layout handles wildcar patterns
     *
     * @var array
     */
    protected $_layoutHandlePatterns = array(
        '^default$',
        '^catalog_category_*',
        '^catalog_product_*',
        '^PRODUCT_*'
    );

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Add not allowed layout handle pattern
     *
     * @param string $pattern
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Chooser_Layout
     */
    public function addLayoutHandlePattern($pattern)
    {
        $this->_layoutHandlePatterns[] = $pattern;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandlePatterns()
    {
        return $this->_layoutHandlePatterns;
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
            ->setName($this->getSelectName())
            ->setId('layout_handle')
            ->setExtraParams('onchange="WidgetInstance.showLayoutBlocksReferance(this.up(\'div.pages\'), this.value)"')
            ->setOptions($this->getLayoutHandles(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme(), Mage::app()->getDefaultStoreView()->getId()));
        return parent::_toHtml().$selectBlock->toHtml();
    }

    /**
     * Retrieve layout handles
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer $storeId
     * @return array
     */
    public function getLayoutHandles($area, $package, $theme, $storeId)
    {
        if (empty($this->_layoutHandles)) {
            /* @var $update Mage_Core_Model_Layout_Update */
            $update = Mage::getModel('core/layout')->getUpdate();
            $this->_layoutHandles[''] = Mage::helper('enterprise_cms')->__('-- Please Select --');
            $this->_collectLayoutHandles($update->getFileLayoutUpdatesXml($area, $package, $theme, $storeId));
        }
        return $this->_layoutHandles;
    }

    /**
     * Filter and collect layout handles into array
     *
     * @param Mage_Core_Model_Layout_Element $layoutHandles
     */
    protected function _collectLayoutHandles($layoutHandles)
    {
        if ($layoutHandlesArr = $layoutHandles->xpath('/*/*/label/..')) {
            foreach ($layoutHandlesArr as $node) {
                if ($this->_filterLayoutHandle($node->getName())) {
                    if ($module = $node->getAttribute('module')) {
                        $helper = Mage::helper($module);
                    } else {
                        $helper = Mage::helper('core');
                    }
                    $this->_layoutHandles[$node->getName()] = $helper->__((string)$node->label);
                }
            }
            asort($this->_layoutHandles, SORT_STRING);
        }
    }

    /**
     * Check if given layout handle allowed (do not match not allowed patterns)
     *
     * @param string $layoutHandle
     * @return boolean
     */
    protected function _filterLayoutHandle($layoutHandle)
    {
        $wildCard = '/('.implode(')|(', $this->getLayoutHandlePatterns()).')/';
        if (preg_match($wildCard, $layoutHandle)) {
            return false;
        }
        return true;
    }
}
