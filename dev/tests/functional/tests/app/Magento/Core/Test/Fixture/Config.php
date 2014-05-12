<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Config
 * Magento configuration settings
 *
 */
class Config extends DataFixture
{
    /**
     * Persist configuration to application
     */
    public function persist()
    {
        Factory::getApp()->magentoCoreApplyConfig($this);
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCoreConfig($this->_dataConfig, $this->_data);
    }
}
