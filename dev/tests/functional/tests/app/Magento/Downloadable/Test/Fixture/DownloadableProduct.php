<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product;

class DownloadableProduct extends Product
{
    const GROUP = 'product_info_tabs_downloadable_items_content';

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
            'constraint' => 'Success',
            'grid_filter' => array('name'),
            'create_url_params' => array(
                'type' => 'downloadable',
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
            'input_prefix' => 'product'
        );

        $data = array(
            'is_virtual' => ['value' => ''], // needed for CURL handler
            'price' => [
                'value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS
            ],
            'downloadable_link_purchase_type' => [
                'value' => 'Yes',
                'input_value' => '1',
                'group' => static::GROUP,
                'input' => 'select',
                'input_name' => 'links_purchased_separately'
            ],
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
