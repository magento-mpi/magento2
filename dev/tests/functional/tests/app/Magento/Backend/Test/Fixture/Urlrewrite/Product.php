<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture\Urlrewrite;

use Mtf\System\Config,
    Mtf\Factory\Factory,
    Mtf\Fixture\DataFixture,
    Magento\Catalog\Test\Fixture\Product as ProductFixture;

/**
 * Class Product
 * URL rewrite product fixture
 *
 * @package Magento\Backend\Test\Fixture\Urlrewrite
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
            ->getMagentoBackendUrlrewriteProduct($this->_dataConfig, $this->_data);

        $this->product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
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
        return $categoryPath . '/' . $this->product->getProductUrl() . '-custom-redirect.html';
    }

    /**
     * Retrieve original request path
     *
     * @return string
     */
    public function getOriginalRequestPath()
    {
        $categoryPath = str_replace(' ', '-', strtolower($this->product->getCategoryName()));
        return $categoryPath . '/' . $this->product->getProductUrl() . '.html';
    }

    /**
     * Retrieve product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->product->getProductId();
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
     * Initialize fixture data
     */
    protected function _initData()
    {
    }
}
