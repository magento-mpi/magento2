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
 * Invitation status history model
 *
 * @method Magento_Invitation_Model_Resource_Invitation_History _getResource()
 * @method Magento_Invitation_Model_Resource_Invitation_History getResource()
 * @method int getInvitationId()
 * @method Magento_Invitation_Model_Invitation_History setInvitationId(int $value)
 * @method string getInvitationDate()
 * @method Magento_Invitation_Model_Invitation_History setInvitationDate(string $value)
 * @method string getStatus()
 * @method Magento_Invitation_Model_Invitation_History setStatus(string $value)
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Invitation_Model_Invitation_History extends Magento_Core_Model_Abstract
{
    /**
     * Invitation Status
     *
     * @var Magento_Invitation_Model_Source_Invitation_Status
     */
    protected $_invitationStatus;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Invitation_Model_Source_Invitation_Status $invitationStatus
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Invitation_Model_Source_Invitation_Status $invitationStatus,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_invitationStatus = $invitationStatus;
        $this->_init('Magento_Invitation_Model_Resource_Invitation_History');
    }

    /**
     * Return status text
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->_invitationStatus->getOptionText($this->getStatus());
    }

    /**
     * Set additional data before saving
     *
     * @return Magento_Invitation_Model_Invitation_History
     */
    protected function _beforeSave()
    {
        $this->setInvitationDate($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
