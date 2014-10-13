<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductManagementInterface
{
    /**
     * Update product process
     *
     * @param  string $productId
     * @param  \Magento\Catalog\Api\Data\ProductInterface $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string $productId
     * @see \Magento\Catalog\WebApi\ProductInterface::update
     */
    public function update($productId, \Magento\Catalog\Api\Data\ProductInterface $product);
}
