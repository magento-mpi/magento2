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
 * Column renderer for customer id
 */
class Magento_Reminder_Block_Adminhtml_Widget_Grid_Column_Renderer_Id
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render customer id linked to its account edit page
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getValue(\Magento\Object $row)
    {
        $customerId = $this->escapeHtml($row->getData($this->getColumn()->getIndex()));
        return '<a href="' . Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('*/customer/edit',
            array('id' => $customerId)) . '">' . $customerId . '</a>';
    }
}
