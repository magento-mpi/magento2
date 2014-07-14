<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\Storage\Data;

use Magento\UrlRedirect\Service\V1\Storage\Data\AbstractData;

/**
 * Product data object
 */
class Product extends AbstractData
{
    /** temporary solution for store product types */
    const TYPE = 'product';

    const TYPE_REDIRECT = 'product_redirect';
}
