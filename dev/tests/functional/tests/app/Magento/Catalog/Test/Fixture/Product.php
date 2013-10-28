<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Product
 *
 * @package Magento\Catalog\Test\Fixture
 */
class Product extends DataFixture
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_PRODUCT_DETAILS     = 'product_info_tabs_product-details';
    const GROUP_ADVANCED_SEO        = 'product_info_tabs_search-optimization';
    const GROUP_PRODUCT_WEBSITE     = 'product_info_tabs_websites';
    const GROUP_PRODUCT_INVENTORY   = 'product_info_tabs_advanced-inventory';

    /**
     * Possible options used for visibility field
     */
    const VISIBILITY_NOT_VISIBLE = 'Not Visible Individually';
    const VISIBILITY_IN_CATALOG = 'Catalog';
    const VISIBILITY_IN_SEARCH = 'Search';
    const VISIBILITY_BOTH = 'Catalog, Search';

    /**
     * List of categories fixtures
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Custom constructor to create product with assigned category
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders =  array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['category::getCategoryName'] = array($this, '_categoryProvider');
        $this->_placeholders['category::getCategoryId'] = array($this, '_categoryProvider');
    }

    /**
     * Get data from repository and reassign it
     */
    public function reset()
    {
        $default = $this->_repository->get('default');

        $this->_dataConfig = $default['config'];
        $this->_data = $default['data'];
    }

    /**
     * Retrieve specify data from category.
     *
     * @param string $placeholder
     * @return mixed
     */
    protected function _categoryProvider($placeholder)
    {
        list($key, $method) = explode('::', $placeholder);
        $product = $this->_getCategory($key);
        return is_callable(array($product, $method)) ? $product->$method() : null;
    }

    /**
     * Create a new category and retrieve category fixture
     *
     * @param string $key
     * @return mixed
     */
    protected function _getCategory($key)
    {
        if (!isset($this->_categories[$key])) {
            $product = Factory::getFixtureFactory()->getMagentoCatalogCategory();
            $product->switchData('subcategory');
            $product->persist();
            $this->_categories[$key] = $product;
        }
        return $this->_categories[$key];
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getData('fields/name/value');
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->getData('fields/sku/value');
    }

    /**
     * Get product price
     *
     * @return string
     */
    public function getProductPrice()
    {
        return $this->getData('fields/price/value');
    }

    /**
     * Get category name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData('category_name');
    }

    /**
     * Get product url
     *
     * @return string
     */
    public function getProductUrl()
    {
        $fields = $this->getData('fields');
        if (isset($fields['url'])) {
            return $fields['url'];
        } else {
            return trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $this->getProductName())), '-');
        }
    }

    /**
     * Get product id
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->getData('fields/id/value');
    }

    /**
     * Create product
     *
     * @return Product
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoCatalogCreateProduct($this);
        $this->_data['fields']['id']['value'] = $id;

        return $this;
    }

    /**
     * Get Url params
     *
     * @param string $urlKey
     * @return string
     */
    public function getUrlParams($urlKey)
    {
        $params = array();
        $config = $this->getDataConfig();
        if (!empty($config[$urlKey]) && is_array($config[$urlKey])) {
            foreach ($config[$urlKey] as $key => $value) {
                $params[] = $key .'/' .$value;
            }
        }
        return implode('/', $params);
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'constraint' => 'Success',

            'block_form_class'  => '\\Magento\\Catalog\\Test\\Block\\Backend\\ProductForm',
            'block_grid_class'  => '\\Magento\\Catalog\\Test\\Block\\Backend\\ProductGrid',

            'grid_filter'       => array('name'),

            'url_create_page'   => 'admin/catalog_product/new',
            'url_update_page'   => 'admin/catalog_product/edit',
            'url_grid_page'     => 'admin/catalog_product/index',

            'create_url_params' => array(
                'type' => 'simple',
                'set'  => 4,
            ),
            'input_prefix' => 'product'
        );

        $this->_data = array(
            'fields' => array(
                'name'   => array(
                    'value' => 'Simple Product %isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'sku'    => array(
                    'value' => 'simple_sku_%isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'price'  => array(
                    'value' => '10',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'tax_class_id' => array(
                    'value' => 'Taxable Goods',
                    'input_value' => '2',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'qty'    => array(
                    'value' => 1000,
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input_name'  => 'product[quantity_and_stock_status][qty]'
                ),
                'weight' => array(
                    'value' => '1',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'meta_title' => array(
                    'value' => 'Meta Title',
                    'group' => static::GROUP_ADVANCED_SEO
                ),
                'meta_keyword' => array(
                    'value' => 'Meta Keyword',
                    'group' => static::GROUP_ADVANCED_SEO
                ),
                'meta_description' => array(
                    'value' => 'Meta Description',
                    'group' => static::GROUP_ADVANCED_SEO
                ),
                'product_website_1' => array(
                    'value' => 'Yes',
                    'input_value' => 1,
                    'group' => static::GROUP_PRODUCT_WEBSITE,
                    'input' => 'checkbox',
                    'input_name'  => 'product[website_ids][]'
                ),
                'inventory_manage_stock' => array(
                    'value' => 'No',
                    'input_value' => '0',
                    'group' => static::GROUP_PRODUCT_INVENTORY,
                    'input' => 'checkbox',
                    'input_name'  => 'product[stock_data][manage_stock]'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogProduct($this->_dataConfig, $this->_data);
    }
}
