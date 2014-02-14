<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Block\Backend\Grid\Column\Renderer;

class Scheduled extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render whether indexer is scheduled
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        if ($this->_getValue($row)) {
            $class = 'grid-severity-notice';
            $text = __('Update by Schedule');
        } else {
            $class = 'grid-severity-major';
            $text = __('Update on Save');
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
