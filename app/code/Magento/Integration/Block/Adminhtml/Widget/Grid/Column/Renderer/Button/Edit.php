<?php
/**
 * Render HTML <button> tag with "edit" action for the integration grid.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

use Magento\Object;
use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

class Edit extends Button
{
    /**
     * Return 'onclick' action for the button (redirect to the integration edit page).
     *
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getOnclickAttribute(Object $row)
    {
        return sprintf("window.location.href='%s'", $this->getUrl('*/*/edit', ['id' => $row->getId()]));
    }

    /**
     * Get title depending on whether element is disabled or not.
     *
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getTitleAttribute(Object $row)
    {
        return $this->_isConfigBasedIntegration($row) ? __('View') : __('Edit');
    }

    /**
     * Get the icon on the grid according to the integration type
     *
     * @param \Magento\Object $row
     * return string
     */
    public function _getClassAttribute(Object $row)
    {
        $class = $this->_isConfigBasedIntegration($row) ? 'info' : 'edit';

        return 'action ' . $class;

    }
}
