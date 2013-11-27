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

namespace Magento\User\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\System\Config;

/**
 * Class Category
 *
 * @package Magento\User\Test\Fixture
 */
class Role extends DataFixture
{
    /**
     * @return $this
     */
    public function persist()
    {
        return Factory::getApp()->magentoUserCreateRole($this);
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoUserRole($this->_dataConfig, $this->_data);
    }

    /**
     * @param array $resource
     */
    public function setResource(array $resource)
    {
        $this->_data['fields']['resource']['value'] = $resource;
    }

    /**
     * @param array $resource
     */
    public function addResource(array $resource)
    {
        $this->_data['fields']['resource']['value'] = array_merge_recursive(
            $this->_data['fields']['resource']['value'],
            $resource
        );
    }
}
