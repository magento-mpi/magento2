<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

interface ReadServiceInterface
{

    /**
     * Get option for configurable product
     *
     * @param string $productSku
     * @param int $optionId
     * @return null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function get($productSku, $optionId);

    /**
     * Get all options for configurable product
     *
     * @param string $productSku
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function getList($productSku);
}
