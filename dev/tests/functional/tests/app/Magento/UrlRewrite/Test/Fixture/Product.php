<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;

/**
 * Class Product
 * URL rewrite product fixture
 */
class Product extends DataFixture
{
    /**
     * Product for which URL rewrite is created
     *
     * @var ProductFixture
     */
    protected $product;

    /**
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['rewritten_product_request_path'] = array($this, 'getRewrittenRequestPath');
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoUrlRewriteProduct($this->_dataConfig, $this->_data);

        $this->product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $this->product->switchData('simple');
        $this->product->persist();
    }

    /**
     * Retrieve rewritten request path
     *
     * @return string
     */
    public function getRewrittenRequestPath()
    {
        $categoryPath = str_replace(' ', '-', strtolower($this->product->getCategoryName()));
        return $categoryPath . '/' . $this->product->getUrlKey() . '-custom-redirect.html';
    }

    /**
     * Retrieve original request path
     *
     * @return string
     */
    public function getOriginalRequestPath()
    {
        $categoryPath = str_replace(' ', '-', strtolower($this->product->getCategoryName()));
        return $categoryPath . '/' . $this->product->getUrlKey() . '.html';
    }

    /**
     * Retrieve product SKU
     *
     * @return int
     */
    public function getProductSku()
    {
        return $this->product->getProductSku();
    }

    /**
     * Retrieve category name to which the product is assigned
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->product->getCategoryName();
    }

    /**
     * Retrieve URL rewrite type
     *
     * @return string
     */
    public function getUrlRewriteType()
    {
        return $this->getData('url_rewrite_type');
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
    }
}
