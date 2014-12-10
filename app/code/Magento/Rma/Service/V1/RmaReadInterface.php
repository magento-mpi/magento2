<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
