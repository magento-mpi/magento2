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
 * Block that renders Design tab
 *
 * @method string getContentBlock()
 * @method string getTabId()
 * @method bool getIsActive()
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setContentBlock($content)
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setIsActive($flag)
 * @method Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setTabId($id)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body extends Magento_Core_Block_Template
{
    /**
     * Get tab content
     *
     * @return string
     */
    public function getContent()
    {
        $content = '';
        $alias = $this->getContentBlock();
        if ($alias) {
            $content = $this->getParentBlock()->getChildHtml($alias);
        }

        return $content;
    }
}
