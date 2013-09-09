<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation grid collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Invitation_Model_Resource_Invitation_Grid_Collection
    extends Magento_Invitation_Model_Resource_Invitation_Collection
{
    /**
     * Join website ID and referrals information (email)
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection|Magento_Invitation_Model_Resource_Invitation_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsiteInformation()->addInviteeInformation();
        return $this;
    }
}
