<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class DownloadableProduct
 * Fixture for Downloadable product
 */
class DownloadableProduct extends Product
{
    const GROUP = 'downloadable_information';

    const LINK_IS_SHAREABLE_NO_VALUE = 0;
    const LINK_IS_SHAREABLE_YES_VALUE = 1;
    const LINK_IS_SHAREABLE_USE_CONFIG_VALUE = 2;

    protected $defaultDataSet = [
        'name' => 'DownloadableProduct_%isolation%',
        'sku' => 'DownloadableProduct_%isolation%',
        'price' => '100',
        'tax_class' => 'Taxable Goods',
        'description' => 'This is description for downloadable product',
        'short_description' => 'This is short description for downloadable product',
        'quantity_and_stock_status_qty' => '1',
        'quantity_and_stock_status' => 'In Stock',
        'is_virtual' => 'Yes',
        'manage_stock' => '-',
        'stock_data_qty' => '-',
        'stock_data_use_config_min_qty' => '-',
        'stock_data_min_qty' => '-',
        'downloadable_sample' => '-',
        'downloadable_links' => 'default',
        'custom_options' => '-',
        'special_price' => '-',
        'group_price' => '-',
        'tier_price' => '-'
    ];

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'grid_filter' => array('name'),
            'create_url_params' => array(
                'type' => 'downloadable',
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
            'input_prefix' => 'product'
        );

        $data = array(
            'is_virtual' => ['value' => '', 'group' => null], // needed for CURL handler
            'price' => [
                'value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS
            ]
        );

        $this->_data['fields'] = $data + $this->_data['fields'];

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDownloadableDownloadableProduct($this->_dataConfig, $this->_data);
    }

    /**
     * Create product
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoDownloadableCreateDownloadable($this);
        $this->_data['fields']['id']['value'] = $id;
    }
}
