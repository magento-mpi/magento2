<?php
/**
 * Store Group fixture
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;
use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

class StoreGroup extends DataFixture
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'website_id' => array(
                    'value' => 1,
                    'input' => 'select'
                ),
                'name' => array(
                    'value' => 'StoreGroup%isolation%'
                ),
                'root_category_id' => array(
                    'value' => 2,
                    'input' => 'select'
                ),
            ),
        );
    }

    /**
     * Create Store
     *
     * @return Store
     */
    public function persist()
    {
        return Factory::getApp()->magentoCoreCreateStoreGroup($this);
    }
}
