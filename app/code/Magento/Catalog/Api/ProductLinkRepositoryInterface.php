<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\MultipleWishlist\Model\Config\Source\Search;

interface ProductLinkRepositoryInterface
{
    /**
     * Save product link
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $entity
     * @return bool
     */
    public function save(\Magento\Catalog\Api\Data\ProductLinkInterface $entity, array $arguments = []);

    /**
     * Delete product link
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $entity
     * @param array $arguments
     * @return bool
     */
    public function delete(\Magento\Catalog\Api\Data\ProductLinkInterface $entity, array $arguments = []);
}
