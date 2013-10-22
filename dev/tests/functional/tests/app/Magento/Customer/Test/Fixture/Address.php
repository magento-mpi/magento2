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
 * Class Address
 * Customer addresses
 *
 * @package Magento\Customer\Address\Fixture
 */
class Address extends DataFixture
{
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
     * Get telephone number
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData('fields/telephone');
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
}
