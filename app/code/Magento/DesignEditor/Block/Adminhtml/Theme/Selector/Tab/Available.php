<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme selector tab for available themes
 */
class Magento_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_Available
    extends Magento_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_TabAbstract
{
    /**
     * Return tab content, available theme list
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getChildBlock('available.theme.list')->setTabId($this->getId());
        return $this->getChildHtml('available.theme.list');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Available Themes');
    }
}
