<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Framework\Api\SearchCriteria;

interface CreditmemoReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Creditmemo
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function search(SearchCriteria $searchCriteria);

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id);
}
