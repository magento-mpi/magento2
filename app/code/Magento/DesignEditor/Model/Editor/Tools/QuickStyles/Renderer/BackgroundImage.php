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
namespace Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer;

class BackgroundImage
    extends \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\AbstractRenderer
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
