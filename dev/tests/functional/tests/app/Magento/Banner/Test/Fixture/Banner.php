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

namespace Magento\Banner\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Banner
 *
 * @package Magento\Banner\Test\Fixture
 */

class Banner extends DataFixture
{
    /**
     * Create banner
     *
     * @return Banner
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoBannerCreateBanner($this);
        $this->_data['fields']['id'] = $id;
        return $this;
    }


    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                // Banner Name = banner1
                'name' => array(
                    'value' => 'banner1'
                ),
                // Active = yes
                'is_enabled' => array(
                    'value' => '1'
                ),
                // Content = text/insert variable
                'store_contents' => array(
                     'value' => array(
                         '0' => '{{config path="general/store_information/name"}}'
                     )
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBannerBanner($this->_dataConfig, $this->_data);
    }
}
