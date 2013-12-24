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

namespace Magento\Customer\Test\Fixture;

use Magento\Core\Test\Fixture;
use Mtf\System\Config;
use Mtf\Factory\Factory;

/**
 * Class Customer Config
 *
 * @package Magento\Core\Test\Fixture
 */
class CustomerConfig extends Fixture\Config
{
    /**
     * Customer Groups
     *
     * @var array
     */
    protected $groups = array();

    /**
     * Construct
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, array $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);
        $this->_placeholders['valid_vat_id_domestic'] = array($this, 'getVatGroup');
        $this->_placeholders['valid_vat_id_union'] = array($this, 'getVatGroup');
        $this->_placeholders['invalid_vat_id'] = array($this, 'getVatGroup');
        $this->_placeholders['validation_error'] = array($this, 'getVatGroup');
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerCustomerConfig($this->_dataConfig, $this->_data);
    }

    /**
     * Get VAT group
     *
     * @param $dataSet
     * @return string
     */
    protected function getVatGroup($dataSet)
    {
        $group = Factory::getFixtureFactory()->getMagentoCustomerCustomerGroup();
        $group->switchData($dataSet);
        $group->persist();
        $this->groups[$group->getGroupId()] = $group->getGroupName();
        return $group->getGroupId();
    }

    /**
     * Get code of valid vat domestic group
     *
     * @return string
     */
    public function getValidVatDomesticGroup()
    {
        $id = $this->getData('sections/customer/groups/create_account/fields/viv_domestic_group/value');
        return $this->groups[$id];
    }

    /**
     * Get code of valid vat intra-union group
     *
     * @return string
     */
    public function getValidVatIntraUnionGroup()
    {
        $id = $this->getData('sections/customer/groups/create_account/fields/viv_intra_union_group/value');
        return $this->groups[$id];
    }

    /**
     * Get code of invalid vat group
     *
     * @return string
     */
    public function getInvalidVatGroup()
    {
        $id = $this->getData('sections/customer/groups/create_account/fields/viv_invalid_group/value');
        return $this->groups[$id];
    }

    /**
     * Get code of validation error group
     *
     * @return string
     */
    public function getValidationErrorGroup()
    {
        $id = $this->getData('sections/customer/groups/create_account/fields/viv_error_group/value');
        return $this->groups[$id];
    }

    /**
     * Get groups ids
     *
     * @return array
     */
    public function getGroupIds()
    {
        return array_keys($this->groups);
    }
}
