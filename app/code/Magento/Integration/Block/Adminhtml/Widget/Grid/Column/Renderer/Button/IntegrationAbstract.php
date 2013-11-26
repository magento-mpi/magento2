<?php
/**
 * Functions that shared both by Edit and Delete buttons.
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;
use Magento\Integration\Model\Integration;
use Magento\Object;

abstract class IntegrationAbstract extends Button
{
    /**
     * Determine whether current integration came from config file, thus can not be removed or edited.
     *
     * @param \Magento\Object $row
     * @return bool
     */
    protected function _isDisabled(Object $row)
    {
        return ($row->hasData(Integration::SETUP_TYPE)
            && (int)$row->getData(Integration::SETUP_TYPE) === Integration::TYPE_CONFIG);
    }
}
