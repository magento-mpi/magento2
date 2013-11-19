<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
 * @package Magento\Core\Test\Fixture
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
