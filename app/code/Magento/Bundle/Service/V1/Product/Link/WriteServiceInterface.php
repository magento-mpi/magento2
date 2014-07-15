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
     * @param \Magento\Bundle\Service\V1\Product\Link\Data\ProductLink $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChild($productSku, $optionId, Data\ProductLink $linkedProduct);
}
