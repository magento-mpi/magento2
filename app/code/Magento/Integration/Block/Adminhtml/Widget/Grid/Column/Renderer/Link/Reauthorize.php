<?php
/**
 * Renders "Re-Authorize" link.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

use Magento\Integration\Model\Integration;
use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

class Reauthorize extends Link
{
    /**
     * {@inheritDoc}
     */
    public function isVisible()
    {
        return $this->_row->getStatus() == Integration::STATUS_ACTIVE;
    }
}
