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
 * Theme selector tab for customized themes
 */
class Magento_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_Customizations
    extends Magento_DesignEditor_Block_Adminhtml_Theme_Selector_Tab_TabAbstract
{
    /**
     * Initialize tab block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('My Customizations');
    }
}
