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

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'type_id' => 'downloadable',
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
            ],
            'qty' => array(
                'value' => 1000,
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input_name' => 'product[quantity_and_stock_status][qty]'
            ),
            'quantity_and_stock_status' => array(
                'value' => 'In Stock',
                'input_value' => 1,
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input_name' => 'product[quantity_and_stock_status][is_in_stock]'
            ),
            'product_website_1' => array(
                'value' => 'Yes',
                'input_value' => 1,
                'group' => static::GROUP_PRODUCT_WEBSITE,
                'input' => 'checkbox',
                'input_name' => 'product[website_ids][]'
            ),

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
