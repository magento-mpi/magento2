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
 * Block that renders Design tab
 *
 * @method string getContentBlock()
 * @method string getTabId()
 * @method bool getIsActive()
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setContentBlock($content)
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setIsActive($flag)
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body setTabId($id)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Body extends Magento_Core_Block_Template
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
