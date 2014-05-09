<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools;

/**
 * Block that renders Design tab
 */
class QuickStyles extends \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Tabs\AbstractTabs
{
    /**
     * @var string Tab HTML identifier
     */
    protected $_htmlId = 'vde-tab-quick-styles';

    /**
     * @var string Tab HTML title
     */
    protected $_title = 'Quick Styles';

    /**
     * Get tabs data
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            array(
                'is_active' => true,
                'id' => 'vde-tab-header',
                'title' => strtoupper(__('Header')),
                'content_block' => 'design_editor_tools_quick-styles_header'
            ),
            array(
                'is_active' => false,
                'id' => 'vde-tab-bgs',
                'title' => strtoupper(__('Backgrounds')),
                'content_block' => 'design_editor_tools_quick-styles_backgrounds'
            ),
            array(
                'is_active' => false,
                'id' => 'vde-tab-buttons',
                'title' => strtoupper(__('Buttons & Icons')),
                'content_block' => 'design_editor_tools_quick-styles_buttons'
            ),
            array(
                'is_active' => false,
                'id' => 'vde-tab-tips',
                'title' => strtoupper(__('Tips & Messages')),
                'content_block' => 'design_editor_tools_quick-styles_tips'
            ),
            array(
                'is_active' => false,
                'id' => 'vde-tab-fonts',
                'title' => strtoupper(__('Fonts')),
                'content_block' => 'design_editor_tools_quick-styles_fonts'
            )
        );
    }

    /**
     * Get the tab state
     *
     * Active tab is showed, while inactive tabs are hidden
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive()
    {
        return true;
    }
}
