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
 * Invitation grid collection
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Resource_Invitation_Grid_Collection
    extends Enterprise_Invitation_Model_Resource_Invitation_Collection
{
    /**
     * Join website ID and referrals information (email)
     *
     * @return Enterprise_Invitation_Model_Resource_Invitation_Collection|Enterprise_Invitation_Model_Resource_Invitation_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsiteInformation()->addInviteeInformation();
        return $this;
    }
}
