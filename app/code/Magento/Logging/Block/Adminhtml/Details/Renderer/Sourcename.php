<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Event source name renderer
 *
 */
namespace Magento\Logging\Block\Adminhtml\Details\Renderer;

class Sourcename extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
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
