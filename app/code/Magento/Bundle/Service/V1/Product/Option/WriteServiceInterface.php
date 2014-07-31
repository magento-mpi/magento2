<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

interface WriteServiceInterface
{
    /**
     * Remove bundle option
     *
     * @param string $productSku
     * @param int $optionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Webapi\Exception
     */
    public function remove($productSku, $optionId);

    /**
     * Add new option for bundle product
     *
     * @param string $productSku
     * @param \Magento\Bundle\Service\V1\Data\Product\Option $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Webapi\Exception
     */
    public function add($productSku, \Magento\Bundle\Service\V1\Data\Product\Option $option);

    /**
     * Update option for bundle product
     *
     * @param string $productSku
     * @param int $optionId
     * @param \Magento\Bundle\Service\V1\Data\Product\Option $option
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Webapi\Exception
     */
    public function update($productSku, $optionId, \Magento\Bundle\Service\V1\Data\Product\Option $option);
}
