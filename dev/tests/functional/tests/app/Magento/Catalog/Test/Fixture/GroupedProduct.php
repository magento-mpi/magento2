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

/**
 * Class GroupedProduct
 * Grouped product data
 *
 * @package Magento\Catalog\Test\Fixture
 */
class GroupedProduct extends Product
{
    const GROUP = 'product_info_tabs_grouped_content';

    /**
     * List of fixtures from created products
     *
     * @var array
     */
    protected $products = array();

    /**
     * Custom constructor to create Grouped product
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['simple::getProductName'] = array($this, 'productProvider');
        $this->_placeholders['virtual::getProductName'] = array($this, 'productProvider');
        $this->_placeholders['downloadable::getProductName'] = array($this, 'productProvider');
        $this->_placeholders['simple::getProductId'] = array($this, 'productProvider');
        $this->_placeholders['virtual::getProductId'] = array($this, 'productProvider');
        $this->_placeholders['downloadable::getProductId'] = array($this, 'productProvider');
    }

    /**
     * Get Associated Product Names
     *
     * @return array
     */
    public function getAssociatedProductNames()
    {
        $names = array();
        foreach ($this->getData('fields/grouped_products/value') as $product) {
            $names[] = $product['search_data']['name'];
        }
        return $names;
    }

    /**
     * Init Data
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'create_url_params' => array(
                'type' => 'grouped',
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
        );
        $this->_data = array(
            'fields' => array(
                'name' => array(
                    'value' => 'Grouped Product %isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'sku' => array(
                    'value' => 'grouped_sku_%isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'grouped_products' => array(
                    'value' => array(
                        'assigned_product_0' => array(
                            'search_data' => array(
                                'name' => '%simple::getProductName%',
                            ),
                            'data' => array(
                                'selection_qty' => array(
                                    'value' => 1
                                ),
                                'product_id' => array(
                                    'value' => '%simple::getProductId%'
                                )
                            )
                        ),
                        'assigned_product_1' => array(
                            'search_data' => array(
                                'name' => '%virtual::getProductName%',
                            ),
                            'data' => array(
                                'selection_qty' => array(
                                    'value' => 1
                                ),
                                'product_id' => array(
                                    'value' => '%virtual::getProductId%'
                                )
                            )
                        ),
                        'assigned_product_2' => array(
                            'search_data' => array(
                                'name' => '%downloadable::getProductName%',
                            ),
                            'data' => array(
                                'selection_qty' => array(
                                    'value' => 1
                                ),
                                'product_id' => array(
                                    'value' => '%downloadable::getProductId%'
                                )
                            )
                        )
                    ),
                    'group' => static::GROUP
                )
            ),
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogGroupedProduct($this->_dataConfig, $this->_data);
    }
}
