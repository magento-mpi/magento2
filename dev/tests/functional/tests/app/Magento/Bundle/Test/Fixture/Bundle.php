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
    const GROUP = 'product_info_tabs_bundle_content';

    /**
     * List of fixtures from created products
     *
     * @var array
     */
    protected $products = array();

    /**
     * Custom constructor to create bundle product with assigned simple products
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['item1_simple1::getProductName'] = array($this, 'productProvider');
        $this->_placeholders['item1_simple1::getProductId'] = array($this, 'productProvider');
        $this->_placeholders['item1_virtual2::getProductName'] = array($this, 'productProvider');
        $this->_placeholders['item1_virtual2::getProductId'] = array($this, 'productProvider');
    }

    /**
     * Retrieve specify data from product.
     *
     * @param string $placeholder
     * @return mixed
     */
    protected function productProvider($placeholder)
    {
        list($productType, $method) = explode('::', $placeholder);
        list(, $productType) = explode('_', $productType);
        $product = $this->getProduct(preg_replace('/\d/', '', $productType));
        return is_callable(array($product, $method)) ? $product->$method() : null;
    }

    /**
     * Create a new product if it was not assigned
     *
     * @param string $productType
     * @throws \InvalidArgumentException
     * @return mixed
     */
    protected function getProduct($productType)
    {
        if (!isset($this->products[$productType])) {
            switch ($productType) {
                case 'simple':
                    $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
                    break;
                case 'virtual':
                    $product = Factory::getFixtureFactory()->getMagentoCatalogVirtualProduct();
                    break;
                default:
                    throw new \InvalidArgumentException(
                        "Product of type '$productType' cannot be added to bundle product."
                    );
            }
            $product->switchData($productType . '_required');
            $product->persist();
            $this->products[$productType] = $product;
        }
        return $this->products[$productType];
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
        foreach ($bundleOptions as $optionData) {
            $optionName = $optionData['title']['value'];
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
        return $prices ? : parent::getProductPrice();
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
            $selectionItem['value'] = $this->getData(
                'fields/bundle_selections/value/' . $option . '/assigned_products/' . $selection . '/search_data/name'
            );
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
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
            'input_prefix' => 'product'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
