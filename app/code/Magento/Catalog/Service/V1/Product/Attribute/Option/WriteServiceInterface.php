<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

use Magento\Catalog\Service\V1\Data\Eav\Option;

/**
 * Interface WriteServiceInterface
 */
interface WriteServiceInterface
{
    /**
     * Add option to attribute
     *
     * @param string $id
     * @param Option $option
     * @return bool
     */
    public function addOption($id, Option $option);
} 