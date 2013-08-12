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
 * Block that renders CSS tab
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Css extends Magento_Core_Block_Template
{
    /**
     * Get file groups content
     *
     * @return array
     */
    public function getFileGroups()
    {
        $groups = array();
        foreach ($this->getCssFiles() as $groupName => $files) {
            $groups[] =  $this->getChildBlock('design_editor_tools_code_css_group')
                ->setTitle($groupName)
                ->setFiles($files)
                ->setThemeId($this->getThemeId())
                ->toHtml();
        }

        return $groups;
    }
}
