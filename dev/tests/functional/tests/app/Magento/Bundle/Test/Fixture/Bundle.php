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
     * Custom constructor to create bundle product with assigned simple products
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders =  array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['item1_product1'] = array($this, 'productProvider');
        $this->_placeholders['item1_product2'] = array($this, 'productProvider');
        $this->_placeholders['item2_product1'] = array($this, 'productProvider');
    }

    /**
     * Create new simple product if they were not assigned
     *
     * @return string
     */
    protected function productProvider()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $fixture->switchData('simple');
        return $fixture->persist()->getProductName();
    }

    /**
     * Assign product to bundle option
     *
     * @param string $option
     * @param array $searchData
     */
    public function assignProduct($option, array $searchData)
    {
        //
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
        $options = array();
        $bundleOptions = $this->getData('fields/bundle_selections/value');
        foreach ($bundleOptions as $option => $optionData) {
            $optionName =  $optionData['title']['value'];
            foreach ($optionData['assigned_products'] as $productData) {
                $options[$optionName] = $productData['search_data']['name'];
            }
        }
        return $options;
    }

    /**
     * Get prices for verification
     *
     * @return array|string
     */
    public function getProductPrice()
    {
        $prices = $this->getData('prices');
        return $prices ? $prices : parent::getProductPrice();
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'constraint' => 'Success',

            'create_url_params' => array(
                'type' => 'bundle',
                'set' => 4,
            )
        );
        $this->_data = array(
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
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'price_type' => array(
                    'value' => 'Fixed',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'price' => array(
                    'value' => '100',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'tax_class_id' => array(
                    'value' => 'Taxable Goods',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'weight_type' => array(
                    'value' => 'Fixed',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'weight' => array(
                    'value' => '1',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'shipment_type' => array(
                    'value' => 'Separately',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'bundle_selections' => array(
                    'value' => array(
                        'bundle_item_0' => array(
                            'title' => array(
                                'value' =>'Drop-down Option'
                            ),
                            'type' => array(
                                'value' => 'Drop-down',
                            ),
                            'required' => array(
                                'value' => 'Yes',
                            ),
                            'assigned_products' => array(
                                'assigned_product_0' => array(
                                    'search_data' => array(
                                        'name' => '%item1_product1%',
                                    ),
                                    'data' => array(
                                        'selection_price_value' => array(
                                            'value' => 10
                                        ),
                                        'selection_price_type' => array(
                                            'value' => 'Fixed',
                                            'input' => 'select'
                                        ),
                                        'selection_qty' => array(
                                            'value' => 1
                                        )
                                    )
                                ),
                                'assigned_product_1' => array(
                                    'search_data' => array(
                                        'name' => '%item1_product2%',
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
                                        )
                                    )
                                )
                            )
                        )
                    ),
                    'group' => static::GROUP_BUNDLE_OPTIONS
                )
            ),
            'prices' => array(
                'price_from' => '110',
                'price_to' => '120'
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
