<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Api;

interface ProductOptionManagementInterface
{
    /**
     * Add new option for bundle product
     *
     * @param \Magento\Bundle\Api\Data\OptionInterface $option
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\Bundle\Service\V1\Product\Option\WriteServiceInterface::add
     * @see \Magento\Bundle\Service\V1\Product\Option\WriteServiceInterface::update
     */
    public function save(\Magento\Bundle\Api\Data\OptionInterface $option);
}
