<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

/**
 * Interface WriteServiceInterface
 */
interface WriteServiceInterface
{
    /**
     * Add option to attribute
     *
     * @param string $id
     * @param \Magento\Catalog\Service\V1\Data\Eav\Option $option
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function addOption($id, \Magento\Catalog\Service\V1\Data\Eav\Option $option);

    /**
     * Delete option from attribute
     *
     * @param string $id
     * @param int $optionId
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function removeOption($id, $optionId);
}
