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
                'invalid' => array(
                    'value' => '123456789',
                ),
                'valid' => array(
                    'value' => '584451913',
                ),
            ),
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData('general_store_information');
        $configFixture->persist();

        $group = Factory::getFixtureFactory()->getMagentoCustomerCustomerGroup();

        foreach ($this->getGroupSetNames() as $set) {
            $group->switchData($set);
            $this->customerGroups[] = array(
                'id' => $this->findId($group->persist(), $group->getGroupName()),
                'code' => $group->getGroupName(),
            );
        }

        $configFixture->switchData('customer_vat');
        $this->prepareConfigVatGroups($configFixture);
        $configFixture->persist();

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
     * Get customer group
     *
     * @return string
     */
    public function getDefaultCustomerGroup()
    {
        return $this->getData('default_group/name/value');
    }

    /**
     * Get customer group
     *
     * @return string
     */
    public function getInvalidVatCustomerGroup()
    {
        return $this->customerGroups[2]['code'];
    }

    /**
     * Get customer group
     *
     * @return string
     */
    public function getValidVatCustomerGroup()
    {
        return $this->customerGroups[1]['code'];
    }

    /**
     * Prepare config VAT groups
     *
     * @param Config $fixture
     */
    protected function prepareConfigVatGroups(Config $fixture)
    {
        $groups = array(
            'sections' => array(
                'customer' => array(
                    'section' => 'customer',
                    'website' => null,
                    'store' => null,
                    'groups' => array(
                        'create_account' => array(
                            'fields' => array(
                                'viv_domestic_group' => array(
                                    'value' => $this->customerGroups[0]['id'],
                                ),
                                'viv_intra_union_group' => array(
                                    'value' => $this->customerGroups[1]['id'],
                                ),
                                'viv_invalid_group' => array(
                                    'value' => $this->customerGroups[2]['id'],
                                ),
                                'viv_error_group' => array(
                                    'value' => $this->customerGroups[3]['id'],
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $fixture->setGroups($groups);
    }

    /**
     * Get Group dataset names
     *
     * @return array
     */
    protected function getGroupSetNames()
    {
        return array(
            'valid_vat_id_domestic',
            'valid_vat_id_union',
            'invalid_vat_id',
            'validation_error',
        );
    }

    /**
     * Get VAT value
     *
     * @param string $type
     * @return string
     */
    public function getVat($type)
    {
        return $this->getData(sprintf('vat/%s/value', $type));
    }

    /**
     * Find id of new customer group in response
     *
     * @param $response
     * @param $name
     * @return string
     */
    protected function findId($response, $name)
    {
        $regExp = '~/customer/group/edit/id/(\d+)(?=.*?' . $name. ')~s';
        preg_match_all($regExp, $response, $matches);
        $result = '';
        if (!empty($matches[1])) {
            $result =  array_pop($matches[1]);;
        }
        return $result;
    }
}
