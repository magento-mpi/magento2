<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Block\Backend\Grid\Column\Renderer;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render indexer status
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $class = '';
        $text = '';
        switch ($this->_getValue($row)) {
            case \Magento\Indexer\Model\Indexer\State::STATUS_INVALID:
                $class = 'grid-severity-critical';
                $text = __('Reindex required');
                break;
            case \Magento\Indexer\Model\Indexer\State::STATUS_VALID:
                $class = 'grid-severity-notice';
                $text = __('Ready');
                break;
            case \Magento\Indexer\Model\Indexer\State::STATUS_WORKING:
                $class = 'grid-severity-major';
                $text = __('Processing');
                break;
        }
        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }
}
