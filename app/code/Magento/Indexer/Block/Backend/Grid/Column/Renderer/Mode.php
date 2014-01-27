<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Block\Backend\Grid\Column\Renderer;

class Mode extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render indexer mode
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $class = '';
        $text = '';
        switch ($this->_getValue($row)) {
            case \Magento\Mview\View\StateInterface::MODE_DISABLED:
                $class = 'grid-severity-notice';
                $text = __('Update on Save');
                break;
            case \Magento\Mview\View\StateInterface::MODE_ENABLED:
                $class = 'grid-severity-major';
                $text = __('Update by Schedule');
                break;
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
