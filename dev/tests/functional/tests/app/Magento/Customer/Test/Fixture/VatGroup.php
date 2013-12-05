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
use Magento\Core\Test\Fixture\Config;

/**
 * Class VAT Customer Group Fixture
 *
 * @package Magento\Customer\Test\Fixture
 */
class VatGroup extends DataFixture
{
    /**
     * Customer fixture
     *
     * @var \Magento\Customer\Test\Fixture\Customer
     */
    protected $customerFixture;

    /**
     * Customer groups
     *
     * @var array
     */
    protected $customerGroups;

    /**
     * Vat config
     *
     * @var \Magento\Core\Test\Fixture\Config
     */
    protected $vatConfig;

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'default_group' => array(
                'name' => array(
                    'value' => 'General',
                ),
            ),
            'vat' => array(
                'uk' => array(
                    'invalid' => array(
                        'value' => '123456789',
                    ),
                    'valid' => array(
                        'value' => '584451913',
                    ),
                ),
            ),
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('general_store_information');
        $config->persist();

        // temporary solution in order to apply placeholders after second switchData method call
        $this->vatConfig = Factory::getFixtureFactory()->getMagentoCustomerCustomerConfig();
        $this->vatConfig->switchData('customer_vat');
        $this->vatConfig->persist();

        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customerFixture->switchData('customer_UK_1');
        Factory::getApp()->magentoCustomerSaveCustomerWithAddress($this->customerFixture);
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customerFixture;
    }

    /**
     * @return \Magento\Customer\Test\Fixture\CustomerConfig
     */
    public function getVatConfig()
    {
        return $this->vatConfig;
    }

    /**
     * Get customer group
     *
     * @return string
     */
    public function getDefaultCustomerGroup()
    {
        return $this->getData('default_group/name/value');
    }

    /**
     * Get VAT value
     *
     * @param string $type
     * @return string
     */
    public function getVatForUk($type)
    {
        return $this->getData(sprintf('vat/uk/%s/value', $type));
    }
}
