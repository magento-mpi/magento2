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
 * Interface ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $id
     * @return Option[]
     */
    public function options($id);
} 