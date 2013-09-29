<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event source name renderer
 *
 */
namespace Magento\Logging\Block\Adminhtml\Details\Renderer;

class Sourcename
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        if (!$data) {
            return '';
        }
        $html = '<div class="source-data"><span class="source-name">' . $row->getSourceName() . '</span>';
        if ($row->getSourceId()) {
            $html .= ' <span class="source-id">#' . $row->getSourceId() . '</span>';
        }
        return $html;
    }
}
