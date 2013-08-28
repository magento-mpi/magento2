<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for Invitee in invitations grid
 *
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render invitee email linked to its account edit page
     *
     * @param   Magento_Object $row
     * @return  string
     */
    protected function _getValue(Magento_Object $row)
    {
        if ($this->_authorization->isAllowed('Magento_Customer::manage')) {
            if (!$row->getReferralId()) {
                return '';
            }
            return '<a href="' . Mage::getSingleton('Magento_Backend_Model_Url')
                ->getUrl('*/customer/edit', array('id' => $row->getReferralId())) . '">'
                   . $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
        } else {
            return parent::_getValue($row);
        }
    }
}
