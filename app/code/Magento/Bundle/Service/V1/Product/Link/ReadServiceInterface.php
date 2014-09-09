<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

interface ReadServiceInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param string $productId
     * @return \Magento\Bundle\Service\V1\Data\Product\Link[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function getChildren($productId);
}
