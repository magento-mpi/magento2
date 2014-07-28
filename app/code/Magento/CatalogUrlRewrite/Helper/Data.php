<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Helper;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;

/**
 * TODO: It is stub class (UrlRewrite)
 *
 * Helper Data
 */
class Data
{
    /**
     * Url slash
     */
    const URL_SLASH = '/';

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * Catalog category helper
     *
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /** @var \Magento\Catalog\Model\Resource\Url  */
    protected $catalogUrl;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @param Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param ProductHelper $productHelper
     * @param CategoryHelper $categoryHelper
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        ProductHelper $productHelper,
        CategoryHelper $categoryHelper,
        \Magento\Catalog\Model\Resource\Url $catalogUrl
    ) {
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        $this->catalogUrl = $catalogUrl;
    }
}
