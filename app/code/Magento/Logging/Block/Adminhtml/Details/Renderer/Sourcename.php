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
class Magento_Logging_Block_Adminhtml_Details_Renderer_Sourcename
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
