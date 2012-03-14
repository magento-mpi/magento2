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
     * Add necessary options
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', Mage::helper('Mage_Widget_Helper_Data')->__('-- Please Select --'));
            $layoutUpdate = $this->_getLayoutUpdate(array(
                'area'    => $this->getArea(),
                'package' => $this->getPackage(),
                'theme'   => $this->getTheme(),
            ));
            $pageTypes = array();
            foreach ($layoutUpdate->getPageTypesHierarchy() as $pageTypeName => $pageTypeInfo) {
                $layoutUpdate->addPageHandles(array($pageTypeName));
                $layoutUpdate->resetUpdates();
                $layoutUpdate->load();
                if (!$layoutUpdate->getContainers()) {
                    continue;
                }
                $pageTypes[$pageTypeName] = $pageTypeInfo;
            }
            $this->_addPageTypeOptions($pageTypes);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve new layout update model instance
     *
     * @param array $arguments
     * @return Mage_Core_Model_Layout_Update
     */
    protected function _getLayoutUpdate(array $arguments)
    {
        return Mage::getModel('Mage_Core_Model_Layout_Update', $arguments);
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
