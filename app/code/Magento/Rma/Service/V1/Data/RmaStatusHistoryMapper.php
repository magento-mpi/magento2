<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

class RmaStatusHistoryMapper
{
    /**
     * rmaStatusHistoryBuilder
     *
     * @var RmaStatusHistoryBuilder
     */
    protected $rmaStatusHistoryBuilder = null;

    /**
     * Mapper constructor
     *
     * @param RmaStatusHistoryBuilder $rmaStatusHistoryBuilder
     */
    public function __construct(\Magento\Rma\Service\V1\Data\RmaStatusHistoryBuilder $rmaStatusHistoryBuilder)
    {
        $this->rmaStatusHistoryBuilder = $rmaStatusHistoryBuilder;
    }

    /**
     * Extract data object from model
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return RmaStatusHistory
     */
    public function extractDto(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->rmaStatusHistoryBuilder->populateWithArray($object->getData());
        $this->rmaStatusHistoryBuilder->setAdmin($object->getIsAdmin());
        $this->rmaStatusHistoryBuilder->setVisibleOnFront($object->getIsVisibleOnFront());
        $this->rmaStatusHistoryBuilder->setCustomerNotified($object->getIsCustomerNotified());
        return $this->rmaStatusHistoryBuilder->create();
    }
}
