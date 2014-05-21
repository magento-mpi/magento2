<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Column renderer for customer email
 */
class Email extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render customer email as mailto link
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    protected function _getValue(\Magento\Framework\Object $row)
    {
        $customerEmail = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="mailto:' . $customerEmail . '">' . $this->escapeHtml($customerEmail) . '</a>';
    }
}
