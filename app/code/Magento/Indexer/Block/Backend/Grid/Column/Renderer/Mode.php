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
            case \Magento\Indexer\Model\Indexer\State::MODE_ON_THE_FLY:
                $class = 'grid-severity-notice';
                $text = __('Update on Save');
                break;
            case \Magento\Indexer\Model\Indexer\State::MODE_CHANGELOG:
                $class = 'grid-severity-major';
                $text = __('Update by schedule');
                break;
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
