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
 * Background image renderer
 */
class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_BackgroundImage
    extends Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
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
        //TODO What if image file does not exists?

        return "{$data['selector']} { {$data['attribute']}: url('{$data['value']}'); }";
    }
}
