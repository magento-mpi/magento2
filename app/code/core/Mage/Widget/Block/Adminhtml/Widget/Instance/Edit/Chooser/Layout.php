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
 * Widget Instance layouts chooser
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout extends Mage_Core_Block_Html_Select
{
    /**
     * Add page type options
     */
    protected function _prepareLayout()
    {
        $this->addOption('', Mage::helper('Mage_Widget_Helper_Data')->__('-- Please Select --'));
        $this->_addPageTypeOptions($this->getLayout()->getUpdate()->getPageTypesHierarchy());
    }

    /**
     * Add page types information to the options
     *
     * @param array $pageTypes
     * @param int $level
     */
    protected function _addPageTypeOptions(array $pageTypes, $level = 0)
    {
        foreach ($pageTypes as $pageTypeName => $pageTypeInfo) {
            $this->addOption($pageTypeName, str_repeat('. ', $level) . $pageTypeInfo['label']);
            $this->_addPageTypeOptions($pageTypeInfo['children'], $level + 1);
        }
    }
}
