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
 * @deprecated
 * @see \Magento\Eav\Api\OptionManagementInterface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @deprecated
     * @see \Magento\Eav\Api\AttributeOptionManagementInterface::getItems
     *
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     */
    public function options($id);
}
