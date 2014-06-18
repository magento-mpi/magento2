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
        /** @var \Magento\Integration\Model\Integration $row */
        $text = parent::render($row);
        $isEndpointSecure = strpos($row->getEndpoint(), 'http:') !== 0;
        $isIdentityLinkSecure = strpos($row->getIdentityLinkUrl(), 'http:') !== 0;
        if (!$isEndpointSecure || !$isIdentityLinkSecure) {
            $text .= '<span class="icon-error"><span>Integration not secure</span></span>';
        }
        return $text;
    }
}
