<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for customer email
 */
namespace Magento\Reminder\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Email
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render customer email as mailto link
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $customerEmail = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="mailto:' . $customerEmail . '">' . $this->escapeHtml($customerEmail) . '</a>';
    }
}
