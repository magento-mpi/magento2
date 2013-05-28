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
 * Block that renders Code tab (or Advanced tab)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Tabs_Abstract
{
    /**
     * Tab HTML identifier
     */
    protected $_htmlId = 'vde-tab-code';

    /**
     * Tab HTML title
     */
    protected $_title = 'Advanced';

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
                'id'          => 'vde-tab-css',
                'title'         => strtoupper($this->__('CSS')),
                'content_block' => 'design_editor_tools_code_css'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-js',
                'title'         => strtoupper($this->__('JS')),
                'content_block' => 'design_editor_tools_code_js'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-custom',
                'title'         => strtoupper($this->__('Custom CSS')),
                'content_block' => 'design_editor_tools_code_custom'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-image-sizing',
                'title'         => strtoupper($this->__('Image Sizing')),
                'content_block' => 'design_editor_tools_code_image_sizing'
            ),
        );
    }
}
