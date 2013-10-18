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
    const GROUP_PRODUCT_DETAILS = 'product_info_tabs_product-details';
    const GROUP_ADVANCED_SEO = 'product_info_tabs_search-optimization';

    /**
     * Possible options used for visibility field
     */
    const VISIBILITY_NOT_VISIBLE = 'Not Visible Individually';
    const VISIBILITY_IN_CATALOG = 'Catalog';
    const VISIBILITY_IN_SEARCH = 'Search';
    const VISIBILITY_BOTH = 'Catalog, Search';

    /**
     * Custom constructor to create product with assigned category
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders =  array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['category'] = array($this, 'categoryProvider');
    }

    /**
     * Create new category
     *
     * @return string
     */
    protected function categoryProvider()
    {
        return Factory::getFixtureFactory()->getMagentoCatalogCategory()
            ->switchData('subcategory')->persist()->getCategoryName();
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
     * Create product
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateProduct($this);

        return $this;
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_defaultConfig = array(
            'block_form_class'  => '\\Magento\\Catalog\\Test\\Block\\Backend\\ProductForm',
            'block_grid_class'  => '\\Magento\\Catalog\\Test\\Block\\Backend\\ProductGrid',

            'grid_filter'       => array('name'),

            'url_create_page'   => 'admin/catalog_product/new',
            'url_update_page'   => 'admin/catalog_product/edit',
            'url_grid_page'     => 'admin/catalog_product/index'
        );

        $this->_repository = array(
            'simple'          => array(
                'config' => array(
                    'test_case' => array('Smart\\Crud', 'Smart\\E2e', 'CheckoutSuite\\Onepage'),
                    'constraint'        => 'Success',

                    'create_url_params' => array(
                        'type' => 'simple',
                        'set'  => 4,
                    )
                ),

                'data' => array(
                    'fields' => array(
                        'name'   => array(
                            'value' => 'Simple Product %isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                        ),
                        'sku'    => array(
                            'value' => 'simple_sku_%isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'price'  => array(
                            'value' => '10',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                        ),
                        'tax_class_id' => array(
                            'value' => 'Taxable Goods',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'qty'    => array(
                            'value' => '1000.00',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'weight' => array(
                            'value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        )
                    )
                )
            ),
            'simple_default_category'          => array(
                'config' => array(
                    'test_case' => array('Smart\\Crud', 'Smart\\E2e', 'CheckoutSuite\\Onepage'),
                    'constraint'        => 'Success',

                    'create_url_params' => array(
                        'set'  => 4,
                        'type' => 'simple',
                    )
                ),

                'data' => array(
                    'fields' => array(
                        'name'   => array(
                            'value' => 'Simple Product %isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                        ),
                        'sku'    => array(
                            'value' => 'simple_sku_%isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'price'  => array(
                            'value' => '10',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                        ),
                        'tax_class_id' => array(
                            'value' => 'Taxable Goods',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'qty'    => array(
                            'value' => '1000.00',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'weight' => array(
                            'value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        )
                    )
                )
            )
        );
        $this->_repository['simple_with_category'] = $this->_repository['simple'];
        $this->_repository['simple_with_category']['data']['category_name'] = '%category%';

        //Default data set
        $this->switchData('simple');
    }
}
