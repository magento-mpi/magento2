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
 * Default css renderer
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Default
    extends Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
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
