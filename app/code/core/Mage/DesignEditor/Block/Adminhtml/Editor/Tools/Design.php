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
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Design extends Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs
{
    /**
     * Tab HTML identifier
     */
    protected $_htmlId = 'vde-tab-design';

    /**
     * Tab HTML title
     */
    protected $_title = 'Design';

    /**
     * Get tabs data
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            array(
                'is_active'     => true,
                'id'          => 'vde-tab-header',
                'title'         => strtoupper($this->__('Header')),
                'content_block' => 'design_editor_tools_design_header'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-bgs',
                'title'         => strtoupper($this->__('Backgrounds')),
                'content_block' => 'design_editor_tools_design_backgrounds'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-buttons',
                'title'         => strtoupper($this->__('Buttons & Icons')),
                'content_block' => 'design_editor_tools_design_buttons'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-tips',
                'title'         => strtoupper($this->__('Tips & Messages')),
                'content_block' => 'design_editor_tools_design_tips'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-fonts',
                'title'         => strtoupper($this->__('Fonts')),
                'content_block' => 'design_editor_tools_design_fonts'
            ),

        );
    }

    /**
     * Get the tab state
     *
     * Active tab is showed, while inactive tabs are hidden
     *
     * @return bool
     */
    public function getIsActive()
    {
        return true;
    }
}
