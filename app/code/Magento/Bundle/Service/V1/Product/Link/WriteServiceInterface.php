<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

interface WriteServiceInterface
{
    /**
     * Remove product from Bundle product option
     *
     * @param string $productSku
     * @param int $optionId
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @return bool
     */
    public function removeChild($productSku, $optionId, $childSku);
}
