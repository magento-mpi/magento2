<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

interface FilterableAttributeListInterface
{
    /**
     * Retrieve list of filterable attributes
     *
     * @return array|\Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getList();
}
