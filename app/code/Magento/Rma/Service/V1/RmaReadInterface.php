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
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Service\V1\Data\RmaSearchResults
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Return list of track data objects based on search criteria
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Track[]
     */
    public function getTracks($id);
}
