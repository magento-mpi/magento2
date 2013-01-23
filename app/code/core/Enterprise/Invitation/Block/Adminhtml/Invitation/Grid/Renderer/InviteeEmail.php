<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manage Invitayions grid invitee email renderer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Grid_Renderer_InviteeEmail
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     *
     * @return bool|string
     */
    public function render(Varien_Object $row)
    {
        return (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Customer::manage'))
            ? 'Enterprise_Invitation_Block_Adminhtml_Invitation_Grid_Column_Invitee' : false;
    }
}
