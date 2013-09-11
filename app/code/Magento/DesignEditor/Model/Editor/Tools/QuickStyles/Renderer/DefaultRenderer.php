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
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer;

class DefaultRenderer
    extends \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\AbstractRenderer
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
