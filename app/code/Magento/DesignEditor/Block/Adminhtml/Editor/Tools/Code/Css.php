<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code;

/**
 * Block that renders CSS tab
 */
class Css extends \Magento\Framework\View\Element\Template
{
    /**
     * Get file groups content
     *
     * @return string[]
     */
    public function getFileGroups()
    {
        $groups = array();
        foreach ($this->getCssFiles() as $groupName => $files) {
            $groups[] = $this->getChildBlock(
                'design_editor_tools_code_css_group'
            )->setTitle(
                $groupName
            )->setFiles(
                $files
            )->setThemeId(
                $this->getThemeId()
            )->toHtml();
        }

        return $groups;
    }
}
