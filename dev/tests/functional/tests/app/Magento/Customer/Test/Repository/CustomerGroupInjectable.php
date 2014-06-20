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
 * Class CustomerGroupInjectable
 */
class CustomerGroupInjectable extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'customer_group_code' => 'testCode_%isolation%',
            'tax_class_id' => ['dataSet' => 'customer_tax_class'],
        ];
    }
}
