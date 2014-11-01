<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

class RmaStatusHistoryBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->_set(RmaStatusHistory::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->_set(RmaStatusHistory::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        $this->_set(RmaStatusHistory::ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerNotified($isCustomerNotified)
    {
        $this->_set(RmaStatusHistory::CUSTOMER_NOTIFIED, (bool)$isCustomerNotified);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibleOnFront($isVisibleOnFront)
    {
        $this->_set(RmaStatusHistory::VISIBLE_ON_FRONT, (bool)$isVisibleOnFront);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->_set(RmaStatusHistory::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdmin($isAdmin)
    {
        $this->_set(RmaStatusHistory::ADMIN, (bool)$isAdmin);
    }
}
