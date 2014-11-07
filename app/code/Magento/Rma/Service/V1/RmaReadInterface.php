<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

interface RmaReadInterface
{
    /**
     * Return data object for specified RMA id
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Rma
     */
    public function get($id);

    /**
     * Return list of rma data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Service\V1\Data\RmaSearchResults
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
