<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class CustomerInjectable
 * Customer repository
 */
class AddressInjectable extends AbstractRepository
{
    /**
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['johndoe_address'] = [
            'company' => 'Magento %isolation%',
            'street' => '6161 West Centinela Avenue',
            'city' => 'Culver City',
            'region_id' => 'California',
            'postcode' => '90230',
            'country_id' => 'United States',
            'telephone' => '555-55-555-55',
            'default_billing' => 'Yes',
            'default_shipping' => 'Yes',
        ];
    }
}
