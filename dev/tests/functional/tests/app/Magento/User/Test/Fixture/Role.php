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

    /**
     * @param array $stores
     * @throws \InvalidArgumentException
     */
    public function setStores(array $stores)
    {
        if(!array_key_exists('gws_store_groups', $this->_data['fields'])) {
            throw new \InvalidArgumentException('Current data set doesn\'t work with stores');
        }

        $this->_data['fields']['gws_store_groups']['value'] = $stores;
    }

    /**
     * @param array $sites
     * @throws \InvalidArgumentException
     */
    public function setWebsites(array $sites)
    {
        if(!array_key_exists('gws_websites', $this->_data['fields'])) {
            throw new \InvalidArgumentException('Current data set doesn\'t work with websites');
        }

        $this->_data['fields']['gws_websites']['value'] = $sites;
    }

    /**
     * Convert data from canonical array to repository native format
     * @param array $data
     * @return array
     */
    protected function convertData(array $data)
    {
        $result = array();
        foreach($data as $key => $value) {
            $result['fields'][$key]['value'] = $value;
        }
        return $result;
    }

    /**
     * @param $name
     * @param array $data
     * @param bool $convert convert data from canonical array to repository native format
     */
    public function save($name, array $data, $convert = true)
    {
        if($convert) {
            $data = $this->convertData($data);
        }
        $this->_repository->set($name, $data);
    }
}
