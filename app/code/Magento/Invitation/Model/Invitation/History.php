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
 * @method \Magento\Invitation\Model\Resource\Invitation\History _getResource()
 * @method \Magento\Invitation\Model\Resource\Invitation\History getResource()
 * @method int getInvitationId()
 * @method \Magento\Invitation\Model\Invitation\History setInvitationId(int $value)
 * @method string getInvitationDate()
 * @method \Magento\Invitation\Model\Invitation\History setInvitationDate(string $value)
 * @method string getStatus()
 * @method \Magento\Invitation\Model\Invitation\History setStatus(string $value)
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Invitation;

class History extends \Magento\Core\Model\AbstractModel
{
    /**
     * Invitation Status
     *
     * @var \Magento\Invitation\Model\Source\Invitation\Status
     */
    protected $_invitationStatus;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Invitation\Model\Source\Invitation\Status $invitationStatus,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_invitationStatus = $invitationStatus;
        $this->_init('Magento\Invitation\Model\Resource\Invitation\History');
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
     * @return \Magento\Invitation\Model\Invitation\History
     */
    protected function _beforeSave()
    {
        $this->setInvitationDate($this->getResource()->formatDate(time()));
        return parent::_beforeSave();
    }
}
