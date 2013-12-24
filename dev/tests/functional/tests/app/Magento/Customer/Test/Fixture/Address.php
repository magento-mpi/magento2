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
use Mtf\Fixture;

/**
 * Class Address
 * Customer addresses
 *
 * @package Magento\Customer\Address\Fixture
 */
class Address extends DataFixture
{
    /**
     * @var Fixture
     */
    protected $_customer;

    /**
     * Format customer address to one line
     *
     * @return string
     */
    public function getOneLineAddress()
    {
        $data = $this->getData();
        $address = isset($data['fields']['prefix']['value']) ? $data['fields']['prefix']['value'] . ' ' : ''
            . $data['fields']['firstname']['value'] . ' '
            . (isset($data['fields']['middlename']['value']) ? $data['fields']['middlename']['value'] . ' ' : '')
            . $data['fields']['lastname']['value'] . ', '
            . (isset($data['fields']['suffix']['value']) ? $data['fields']['suffix']['value'] . ' ' : '')
            . $data['fields']['street_1']['value'] . ', '
            . $data['fields']['city']['value'] . ', '
            . $data['fields']['region']['value'] . ' '
            . $data['fields']['postcode']['value'] . ', '
            . $data['fields']['country']['value'];

        return $address;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData('fields/city/value');
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getData('fields/country/value');
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getData('fields/firstname');
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getData('fields/lastname');
    }

    /**
     * Get postal code
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->getData('fields/postcode/value');
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->getData('fields/region/value');
    }

    /**
     * Get telephone number
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData('fields/telephone');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerAddress($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('address_US_1');
    }

    /**
     * Set customer
     *
     * @param Fixture $customer
     */
    public function setCustomer(Fixture $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        Factory::getApp()->magentoCustomerCreateAddress($this);
    }

    /**
     * Get customer
     *
     * @return Fixture
     */
    public function getCustomer()
    {
        return $this->_customer;
    }
}
