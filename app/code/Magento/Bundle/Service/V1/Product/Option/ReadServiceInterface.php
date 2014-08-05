<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

interface ReadServiceInterface
{
    /**
     * Get option for bundle product
     *
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\Bundle\Service\V1\Data\Product\Option
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function get($productSku, $optionId);

    /**
     * Get all options for bundle product
     *
     * @param string $productSku
     * @return \Magento\Bundle\Service\V1\Data\Product\Option[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function getList($productSku);
}
