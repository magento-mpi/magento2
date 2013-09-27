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
 * Invitation status option source
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Model_Source_Invitation_Options
    implements Magento_Core_Model_Option_ArrayInterface

{
    /**
     * Invitation Status
     *
     * @var Magento_Invitation_Model_Source_Invitation_Status
     */
    protected $_invitationStatus;

    /**
     * @param Magento_Invitation_Model_Source_Invitation_Status $invitationStatus
     */
    function __construct(
        Magento_Invitation_Model_Source_Invitation_Status $invitationStatus
    ) {
        $this->_invitationStatus = $invitationStatus;
    }

    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_invitationStatus->getOptions();
    }
}
