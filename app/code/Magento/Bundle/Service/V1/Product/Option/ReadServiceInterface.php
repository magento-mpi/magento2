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
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\Bundle\Service\V1\Data\Option\Metadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku, $optionId);

    /**
     * @return \Magento\Bundle\Service\V1\Data\Option\Type\Metadata[]
     */
    public function getTypes();

    /**
     * @param string $productSku
     * @return \Magento\Bundle\Service\V1\Data\Option\Metadata[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAll($productSku);
}
