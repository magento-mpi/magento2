<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class BannerRotatorWidget Fixture
 *
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
