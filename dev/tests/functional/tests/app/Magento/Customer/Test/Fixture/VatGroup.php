<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class VAT Customer Group Fixture
 *
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
     * @var \Magento\Customer\Test\Fixture\CustomerConfig
     */
    protected $vatConfig;

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = [
            'default_group' => [
                'name' => [
                    'value' => 'General',
                ],
            ],
            'vat' => [
                'invalid' => [
                    'value' => '123456789',
                ],
                'valid' => [
                    'value' => '584451913', //Valid GB VAT number
                ],
            ],
            'customer' => [
                'dataset' => [
                    'value' => 'customer_UK_1',
                ],
            ],
        ];

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerVatGroup($this->_dataConfig, $this->_data);
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

        $customerDataset = $this->getData('customer/dataset/value');
        $this->customerFixture = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customerFixture->switchData($customerDataset);
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
     * Get customer group
     *
     * @return string
     */
    public function getDefaultCustomerGroup()
    {
        return $this->getData('default_group/name/value');
    }

    /**
     * Get Invalid VAT id group
     *
     * @return string
     */
    public function getInvalidVatGroup()
    {
        return $this->vatConfig->getInvalidVatGroup();
    }

    /**
     * Get group name for valid VAT intra-union
     *
     * @return string
     */
    public function getValidVatIntraUnionGroup()
    {
        return $this->vatConfig->getValidVatIntraUnionGroup();
    }

    /**
     * Get groups ids
     *
     * @return array
     */
    public function getGroupsIds()
    {
        return $this->vatConfig->getGroupIds();
    }

    /**
     * Get invalid VAT number
     *
     * @return string
     */
    public function getInvalidVatNumber()
    {
        return $this->getData('vat/invalid/value');
    }

    /**
     * Get valid VAT number
     *
     * @return string
     */
    public function getValidVatNumber()
    {
        return $this->getData('vat/valid/value');
    }
}
