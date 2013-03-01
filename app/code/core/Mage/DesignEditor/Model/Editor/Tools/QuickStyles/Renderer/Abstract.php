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
 * Quick style abstract renderer
 */
abstract class Mage_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
{
    /**
     * Render CSS
     *
     * @param array $data
     * @return string
     */
    public function toCss($data)
    {
        return $this->_render($data);
    }

    /**
     * Render concrete element
     *
     * @param array $data
     * @return string
     */
    abstract protected function _render($data);
}
