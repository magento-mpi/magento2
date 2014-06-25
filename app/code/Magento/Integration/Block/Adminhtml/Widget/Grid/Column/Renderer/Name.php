<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Integration Name Renderer
 */
class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render integration name.
     *
     * If integration endpoint URL is unsecure then add error message to integration name.
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $text = parent::render($row);
        if (strpos($row->getEndpoint(), 'http:') === 0) {
            $text .= '<span class="icon-error"><span>Integration not secure</span></span>';
        }
        return $text;
    }
}
