<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools;

/**
 * Block that renders Code tab (or Advanced tab)
 */
class Code
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Tabs\AbstractTabs
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
                'title'         => strtoupper(__('CSS')),
                'content_block' => 'design_editor_tools_code_css'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-js',
                'title'         => strtoupper(__('JS')),
                'content_block' => 'design_editor_tools_code_js'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-custom',
                'title'         => strtoupper(__('Custom CSS')),
                'content_block' => 'design_editor_tools_code_custom'
            ),
            array(
                'is_active'     => false,
                'id'          => 'vde-tab-image-sizing',
                'title'         => strtoupper(__('Image Sizing')),
                'content_block' => 'design_editor_tools_code_image_sizing'
            ),
        );
    }
}
