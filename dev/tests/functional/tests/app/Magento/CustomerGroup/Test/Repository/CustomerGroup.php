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

namespace Magento\CustomerGroup\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerGroup Repository
 *
 * @package Magento\CustomerGroup\Test\Fixture
 */
class CustomerGroup extends AbstractRepository
{
    /**
     *  customer group curl identifier
     */
    const CUSTOMER_GROUP_CURL = 'customer_group_curl';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array('config' => $defaultConfig, 'data' => $defaultData);
        $this->_data[self::CUSTOMER_GROUP_CURL] = $this->_getCustomerGroupCurl();
    }

    /**
     * Get the customer group data for curl
     *
     * @return array
     */
    protected function _getCustomerGroupCurl()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'code' => array(
                        'value' => 'Test'
                    ),
                    'tax_class' => array(
                        'value' => '3'
                    )
                )
            )
        );
    }
}
