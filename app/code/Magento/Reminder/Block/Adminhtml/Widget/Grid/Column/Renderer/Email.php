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
class Magento_Reminder_Block_Adminhtml_Widget_Grid_Column_Renderer_Email
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
