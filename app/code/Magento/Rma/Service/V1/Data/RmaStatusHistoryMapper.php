<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

use Magento\Rma\Model\Rma\Status\History;

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
     * @param History $historyModel
     * @return RmaStatusHistory
     */
    public function extractDto(History $historyModel)
    {
        $this->rmaStatusHistoryBuilder->populateWithArray($historyModel->getData());
        $this->rmaStatusHistoryBuilder->setAdmin($historyModel->getIsAdmin());
        $this->rmaStatusHistoryBuilder->setVisibleOnFront($historyModel->getIsVisibleOnFront());
        $this->rmaStatusHistoryBuilder->setCustomerNotified($historyModel->getIsCustomerNotified());
        return $this->rmaStatusHistoryBuilder->create();
    }
}
