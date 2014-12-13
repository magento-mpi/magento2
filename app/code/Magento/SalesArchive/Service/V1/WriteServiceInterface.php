<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Service\V1;

use Magento\Framework\Api\SearchCriteria;

interface WriteServiceInterface
{
    /**
     * Return List of archived orders service
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\SalesArchive\Service\V1\Data\ArchiveSearchResults
     */
    public function getList(SearchCriteria $searchCriteria);

    /**
     * @return bool
     */
    public function moveOrdersToArchive();

    /**
     * @param int $id
     * @return bool
     */
    public function removeOrderFromArchiveById($id);
}
