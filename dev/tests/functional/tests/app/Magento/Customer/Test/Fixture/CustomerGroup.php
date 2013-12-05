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

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Customer Group Fixture
 *
 * @package Magento\Customer\Test\Fixture
 */
class CustomerGroup extends DataFixture
{
    /**
     * Create customer via frontend
     */
    public function persist()
    {
        return Factory::getApp()->magentoCustomerCreateCustomerGroup($this);
    }

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'code' => array(
                    'value' => 'Test group %isolation%',
                ),
                'tax_class' => array(
                    'value' => 'Retail Customer',
                    'input_value' => 3,
                ),
            ),
        );
        $this->_defaultConfig = array();

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerCustomerGroup($this->_dataConfig, $this->_data);
    }

    /**
     * Get group name
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->getData('fields/code/value');
    }
}
