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
 * Quick style abstract renderer
 */
abstract class Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Abstract
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
