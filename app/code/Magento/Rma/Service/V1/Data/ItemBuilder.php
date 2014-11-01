<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject as ExtensibleObject;
use Magento\Framework\Api\AbstractExtensibleObjectBuilder;
use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Rma\Service\V1\RmaMetadataReadInterface;

/**
 * Builder for the Rma Item Service Data Object
 *
 * @method Item create()
 * @method Item mergeDataObjectWithArray(ExtensibleObject $dataObject, array $data)
 * @method Item mergeDataObjects(ExtensibleObject $firstDataObject, ExtensibleObject $secondDataObject)
 */
class ItemBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param RmaMetadataReadInterface $metadataService
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        RmaMetadataReadInterface $metadataService
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
    }


    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->_set(\Magento\Rma\Service\V1\Data\Item::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($orderItemId)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyRequested($qtyRequested)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::QTY_REQUESTED, $qtyRequested);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyApproved($qtyApproved)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::QTY_APPROVED, $qtyApproved);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyAuthorized($qtyAuthorized)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::QTY_AUTHORIZED, $qtyAuthorized);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyReturned($qtyReturned)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::QTY_RETURNED, $qtyReturned);
    }

    /**
     * {@inheritdoc}
     */
    public function setReason($reason)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::REASON, $reason);
    }

    /**
     * {@inheritdoc}
     */
    public function setCondition($condition)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::CONDITION, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolution($resolution)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::RESOLUTION, $resolution);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->_set(\Magento\Rma\Service\V1\Data\Item::STATUS, $status);
    }
}
