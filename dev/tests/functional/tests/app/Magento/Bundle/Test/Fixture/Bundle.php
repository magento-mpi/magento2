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
use Magento\Catalog\Test\Fixture\Product;

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
            $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
            $product->switchData('simple_required');
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
        $prices = $this->getData('checkout/prices');
        return $prices ? $prices : parent::getProductPrice();
    }

    /**
     * Get options type, value and qty to select for adding to shopping cart
     *
     * @return array
     */
    public function getSelectionData()
    {
        $options = $this->getData('checkout/selection');
        $selectionData = array();
        foreach ($options as $option => $selection) {
            $selectionItem['type'] = $this->getData('fields/bundle_selections/value/' . $option . '/type/input_value');
            $selectionItem['qty'] = $this->getData(
                'fields/bundle_selections/value/' . $option .
                '/assigned_products/' . $selection . '/data/selection_qty/value'
            );
            $selectionItem['value'] = $this->getData('fields/bundle_selections/value/' . $option .
                '/assigned_products/' . $selection . '/search_data/name');
            $selectionData[] = $selectionItem;
        }
        return $selectionData;
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'create_url_params' => array(
                'type' => 'bundle',
                'set' => 4,
            ),
            'input_prefix' => 'product'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
