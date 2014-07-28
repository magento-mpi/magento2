<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog url model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model;

/**
 * @TODO: UrlRewrite: MAGETWO-26285 Looks like we can get rid of this class
 */
class Url
{
    /**
     * Resource model
     *
     * @var \Magento\Catalog\Model\Resource\Url
     */
    protected $_resourceModel;

    /**
     * Categories cache for products
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Store root categories cache
     *
     * @var array
     */
    protected $_rootCategories = array();

    /**
     * Rewrite cache
     *
     * @var array
     */
    protected $_rewrites = array();

    /**
     * Current url rewrite rule
     *
     * @var \Magento\Framework\Object
     */
    protected $_rewrite;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct = null;

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_catalogCategory = null;

    /**
     * Url factory
     *
     * @var \Magento\Catalog\Model\Resource\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $productUrl;

    /** @var \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /**
     * @param Resource\UrlFactory $urlFactory
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param Product\Url $productUrl
     * @param \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\UrlFactory $urlFactory,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Helper\Data $catalogData,
        Product\Url $productUrl,
        \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator $categoryUrlPathGenerator
    ) {
        $this->_urlFactory = $urlFactory;
        $this->_catalogCategory = $catalogCategory;
        $this->_catalogProduct = $catalogProduct;
        $this->_catalogData = $catalogData;
        $this->productUrl = $productUrl;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return \Magento\Store\Model\Store|\Magento\Store\Model\Store[]
     */
    public function getStores($storeId = null)
    {
        return $this->getResource()->getStores($storeId);
    }

    /**
     * Retrieve resource model
     *
     * @return \Magento\Catalog\Model\Resource\Url
     */
    public function getResource()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = $this->_urlFactory->create();
        }
        return $this->_resourceModel;
    }
}
