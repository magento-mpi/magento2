<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Option;

/**
 * Interface ReadServiceInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @deprecated
     * @see \Magento\Catalog\Api\Product\Attribute\OptionManagementInterface::getList
     *
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     */
    public function options($id);
}
