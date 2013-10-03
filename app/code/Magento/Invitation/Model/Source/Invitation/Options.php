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
namespace Magento\Invitation\Model\Source\Invitation;

class Options
    implements \Magento\Core\Model\Option\ArrayInterface

{
    /**
     * Invitation Status
     *
     * @var \Magento\Invitation\Model\Source\Invitation\Status
     */
    protected $_invitationStatus;

    /**
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     */
    function __construct(
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
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
