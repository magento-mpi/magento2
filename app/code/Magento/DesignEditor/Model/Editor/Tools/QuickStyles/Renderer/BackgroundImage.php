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
 * Background image renderer
 */
class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage
    extends Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
{
    /**
     * Render color picker attribute
     *
     * Return format:
     * .content { background-image: url(path/image.png); }
     *
     * @param array $data
     * @return string
     */
    protected function _render($data)
    {
        $override = "none";

        if (!empty($data['value'])) {
            $override = "url('{$data['value']}')";
        }

        return "{$data['selector']} { {$data['attribute']}: " . $override . "; }";
    }
}
