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
     * Add child product to specified Bundle option
     *
     * @param string $productSku
     * @param int $optionId
     * @param \Magento\Bundle\Service\V1\Data\Product\Link $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChild($productSku, $optionId, \Magento\Bundle\Service\V1\Data\Product\Link $linkedProduct);

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
