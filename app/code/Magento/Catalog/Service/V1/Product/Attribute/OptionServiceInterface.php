<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Service\V1\Data\ProductAttributeType;

/**
 * Class Product Attribute Option Service Interface
 */
interface OptionServiceInterface
{
    /**
     * Retrieve list of attribute options
     *
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function options($id);

    /**
     * Add option to attribute
     *
     * @param string $id
     * @param \Magento\Catalog\Service\V1\Data\Eav\Option $option
     * @return bool
     */
    public function addOption($id, \Magento\Catalog\Service\V1\Data\Eav\Option $option);
}
