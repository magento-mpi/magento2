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
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code;

class Css extends \Magento\Core\Block\Template
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
