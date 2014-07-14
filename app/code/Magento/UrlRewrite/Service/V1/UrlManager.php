<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product as ProductStorage;
use Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory;
use Magento\UrlRedirect\Service\V1\StorageInterface;

use Magento\UrlRedirect\Service\V1\Storage\Data\Converter;
use Magento\CatalogUrlRewrite\Model\Product\GeneratorFactory;
use Magento\Framework\App\Resource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Model\Config;

/**
 * Product Generator
 * // TODO: it is draft class name
 */
class UrlManager
{
    public function saveUrls(array $urls)
    {
        $this->storage->save($urls);
    }
}
