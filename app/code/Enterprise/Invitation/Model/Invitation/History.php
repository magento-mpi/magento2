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
 * Invitation status history model
 *
 * @method Enterprise_Invitation_Model_Resource_Invitation_History _getResource()
 * @method Enterprise_Invitation_Model_Resource_Invitation_History getResource()
 * @method int getInvitationId()
 * @method Enterprise_Invitation_Model_Invitation_History setInvitationId(int $value)
 * @method string getInvitationDate()
 * @method Enterprise_Invitation_Model_Invitation_History setInvitationDate(string $value)
 * @method string getStatus()
 * @method Enterprise_Invitation_Model_Invitation_History setStatus(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Invitation_History extends Magento_Core_Model_Abstract
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Invitation_Model_Resource_Invitation_History');
    }

    /**
     * Return status text
     *
     * @return string
     */
    public function getStatusText()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Source_Invitation_Status')->getOptionText(
            $this->getStatus()
        );
    }

    /**
     * Set additional data before saving
     *
     * @return Enterprise_Invitation_Model_Invitation_History
     */
    protected function _beforeSave()
    {
        $this->setInvitationDate($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
