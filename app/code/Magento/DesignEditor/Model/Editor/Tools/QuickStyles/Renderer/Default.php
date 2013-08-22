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
 * Default css renderer
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Default
    extends Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
{
    /**
     * Render concrete element
     *
     * Return format:
     * .header #title { color: red; }
     *
     * @param array $data
     * @return string
     */
    protected function _render($data)
    {
        return "{$data['selector']} { {$data['attribute']}: {$data['value']}; }";
    }
}
