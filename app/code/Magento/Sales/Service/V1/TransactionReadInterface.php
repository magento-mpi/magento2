<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

use Magento\Framework\Api\SearchCriteria;

interface TransactionReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Transaction
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Service\V1\Data\TransactionSearchResults
     */
    public function search(SearchCriteria $searchCriteria);
}
