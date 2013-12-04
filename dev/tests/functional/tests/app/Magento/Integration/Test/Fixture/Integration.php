<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Integration data fixture.
 */
class Integration extends DataFixture
{
    /**
     * Save integration fixture.
     */
    public function persist()
    {
        Factory::getApp()->magentoIntegrationCreateIntegration($this);
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()->getMagentoIntegrationIntegration(
            $this->_dataConfig,
            $this->_data
        );
        $this->switchData(\Magento\Integration\Test\Repository\Integration::INTEGRATION_TAB);
    }

    /**
     * Get integration name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('fields/name/value');
    }
}
