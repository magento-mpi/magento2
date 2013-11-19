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

use Magento\Integration\Model\Integration;
use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;
use Magento\Object;

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

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getUrl(Object $row)
    {
        return 'javascript:void(0);';
    }

    /**
     * {@inheritDoc}
     */
    protected function _getDataAttributes()
    {
        $isIntegrationActive = $this->_row->getStatus() === Integration::STATUS_ACTIVE;

        return [
            'mage-init' => [
                'integrationPopup' => [
                    'dialog' => $isIntegrationActive ? 'deactivate' : 'permissions',
                    'name' => $this->_row->getName(),
                    'url' => $this->getUrl($this->getUrlPattern(), ['id' => $this->_row->getId()]),
                ]
            ]
        ];
    }
}
