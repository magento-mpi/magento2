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

namespace Magento\Widget\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class BannerRotatorWidget Fixture
 *
 * @package Magento\Widget\Test\Fixture
 */

class BannerRotatorWidget extends Widget
{
    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoWidgetBannerRotatorWidget($this->_dataConfig, $this->_data);

        $default = $this->_repository->get('default');

        $this->_dataConfig = $default['config'];
        $this->_data = $default['data'];
    }
}
