<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Api;

interface OptionRepositoryInterface
{
    /**
     * Get option for configurable product
     *
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\ConfigurableProduct\Api\Data\OptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\ReadServiceInterface::get
     */
    public function get($productSku, $optionId);

    /**
     * Get all options for configurable product
     *
     * @param string $productSku
     * @return \Magento\ConfigurableProduct\Api\Data\OptionInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\ReadServiceInterface::getList
     */
    public function getList($productSku);

    /**
     * Remove option from configurable product
     *
     * @param Data\OptionInterface $option
     * @return bool
     */
    public function delete(\Magento\ConfigurableProduct\Api\Data\OptionInterface $option);

    /**
     * Remove option from configurable product
     *
     * @param string $productSku
     * @param int $optionId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\WriteServiceInterface::remove
     */
    public function deleteById($productSku, $optionId);

    /**
     * Save option
     *
     * @param string $productSku
     * @param \Magento\ConfigurableProduct\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \InvalidArgumentException
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\WriteServiceInterface::add
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\WriteServiceInterface::update
     */
    public function save($productSku, \Magento\ConfigurableProduct\Api\Data\OptionInterface $option);
}
