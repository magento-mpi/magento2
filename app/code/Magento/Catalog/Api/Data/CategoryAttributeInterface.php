<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

interface CategoryAttributeInterface extends \Magento\Catalog\Api\Data\EavAttributeInterface
{
    const ENTITY_TYPE_CODE = 'catalog_category';

    const DEFAULT_ATTRIBUTE_SET_ID = 3;
}
