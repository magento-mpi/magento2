<?php
/**
 * Renders "Activate" link.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

use \Magento\Integration\Model\Integration;
use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

class Activate extends Link
{
    /**
     * {@inheritDoc}
     */
    public function getUrlPattern()
    {
        return ($this->_row->getStatus() == Integration::STATUS_INACTIVE) ? '*/*/activate' : '*/*/deactivate';
    }

    /**
     * {@inheritDoc}
     */
    public function getCaption()
    {
        return ($this->_row->getStatus() == Integration::STATUS_INACTIVE) ? __('Activate') : __('Deactivate');
    }
}
