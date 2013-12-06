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
     * @param string $productData
     * @return string
     */
    protected function formatProductType($productData)
    {
        list(, $productData) = explode('_', $productData);
        return preg_replace('/\d/', '', $productData);
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
