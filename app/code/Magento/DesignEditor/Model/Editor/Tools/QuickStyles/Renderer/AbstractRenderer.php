<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer;

/**
 * Quick style abstract renderer
 */
abstract class AbstractRenderer
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
