<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Backend Grid Renderer
 */
class UnsecureUrl extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render contents as a long text
     *
     * Text will be truncated as specified in string_limit, truncate or 250 by default
     * Also it can be html-escaped and nl2br()
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $truncateLength = 250;
        // stringLength() is for legacy purposes
        if ($this->getColumn()->getStringLimit()) {
            $truncateLength = $this->getColumn()->getStringLimit();
        }
        if ($this->getColumn()->getTruncate()) {
            $truncateLength = $this->getColumn()->getTruncate();
        }
        $text = $this->filterManager->truncate(parent::_getValue($row), array('length' => $truncateLength));
        if ($this->getColumn()->getEscape()) {
            $text = $this->escapeHtml($text);
        }
        if ($this->getColumn()->getNl2br()) {
            $text = nl2br($text);
        }
        return $text . (strpos($row->getEndpoint(),'https') === false ? '<span class="icon-error"><span>Integration not secure</span></span>' : '');
    }
}
