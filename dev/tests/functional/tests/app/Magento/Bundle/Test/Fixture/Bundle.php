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

namespace Magento\Bundle\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product as Product;

/**
 * Class Bundle
 *
 * @package Magento\Bundle\Test\Fixture
 */
class Bundle extends Product
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_BUNDLE_OPTIONS = 'product_info_tabs_bundle_content';

    /**
     * List of fixtures from created products
     *
     * @var array
     */
    protected $_products = array();

    /**
     * Custom constructor to create bundle product with assigned simple products
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders =  array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['item1_product1::getProductName'] = array($this, '_productProvider');
        $this->_placeholders['item1_product2::getProductName'] = array($this, '_productProvider');
        $this->_placeholders['item1_product1::getProductId'] = array($this, '_productProvider');
        $this->_placeholders['item1_product2::getProductId'] = array($this, '_productProvider');
    }

    /**
     * Retrieve specify data from product.
     *
     * @param string $placeholder
     * @return mixed
     */
    protected function _productProvider($placeholder)
    {
        list($key, $method) = explode('::', $placeholder);
        $product = $this->_getProduct($key);
        return is_callable(array($product, $method)) ? $product->$method() : null;
    }

    /**
     * Create a new product if it was not assigned
     *
     * @param string $key
     * @return mixed
     */
    protected function _getProduct($key)
    {
        if (!isset($this->_products[$key])) {
            $product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
            $product->switchData('simple');
            $product->persist();
            $this->_products[$key] = $product;
        }
        return $this->_products[$key];
    }

    /**
     * Create bundle product
     *
     * @return $this|void
     */
    public function persist()
    {
        Factory::getApp()->magentoBundleCreateBundle($this);

        return $this;
    }

    /**
     * Get bundle options data to add product to shopping cart
     */
    public function getBundleOptions()
    {
        return $this->getData('checkout/bundle_options');
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = array(
            'bundle_fixed' => array(
                'config' => array(
                    'constraint' => 'Success',
                    'create_url_params' => array(
                        'type' => 'bundle',
                        'set' => 4,
                    ),
                    'input_prefix' => 'product',
                ),
                'data' => array(
                    'fields' => array(
                        'name' => array(
                            'value' => 'Bundle Fixed Product Required %isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'sku' => array(
                            'value' => 'bundle_sku_fixed_%isolation%',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'sku_type' => array(
                            'value' => 'Fixed',
                            'input_value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'price_type' => array(
                            'value' => 'Fixed',
                            'input_value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'price' => array(
                            'value' => '100',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'tax_class_id' => array(
                            'value' => 'Taxable Goods',
                            'input_value' => '2',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'weight_type' => array(
                            'value' => 'Fixed',
                            'input_value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'weight' => array(
                            'value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS
                        ),
                        'shipment_type' => array(
                            'value' => 'Separately',
                            'input_value' => '1',
                            'group' => static::GROUP_PRODUCT_DETAILS,
                            'input' => 'select'
                        ),
                        'bundle_selections' => array(
                            'value' => array(
                                'bundle_item_0' => array(
                                    'title' => array(
                                        'value' => 'Drop-down Option',
                                    ),
                                    'type' => array(
                                        'value' => 'Drop-down',
                                        'input_value' => 'select'
                                    ),
                                    'required' => array(
                                        'value' => 'Yes',
                                        'input_value' => '1'
                                    ),
                                    'assigned_products' => array(
                                        'assigned_product_0' => array(
                                            'search_data' => array(
                                                'name' => '%item1_product1::getProductName%',
                                            ),
                                            'data' => array(
                                                'selection_price_value' => array(
                                                    'value' => 10
                                                ),
                                                'selection_price_type' => array(
                                                    'value' => 'Fixed',
                                                ),
                                                'selection_qty' => array(
                                                    'value' => 1
                                                ),
                                                'product_id' => array(
                                                    'value' => '%item1_product1::getProductId%'
                                                )
                                            )
                                        ),
                                        'assigned_product_1' => array(
                                            'search_data' => array(
                                                'name' => '%item1_product2::getProductName%',
                                            ),
                                            'data' => array(
                                                'selection_price_value' => array(
                                                    'value' => 20
                                                ),
                                                'selection_price_type' => array(
                                                    'value' => 'Percent',
                                                    'input' => 'select'
                                                ),
                                                'selection_qty' => array(
                                                    'value' => 1
                                                ),
                                                'product_id' => array(
                                                    'value' => '%item1_product2::getProductId%'
                                                )
                                            )
                                        ),
                                    ),
                                ),
                            ),
                            'group' => static::GROUP_BUNDLE_OPTIONS
                        )
                    )
                )
            )
        );
    }
}
