<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Data\Eav\Option as EavOption;

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
    public function addOption($id, EavOption $option);
} 