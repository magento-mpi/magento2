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

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Attribute Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ProductAttribute extends AbstractRepository
{
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['configurable_attribute'] = $this->_data['default'];
        $this->_data['configurable_attribute']['data']['fields']['option[value][option_0][0]']['value'] = 'option1';
        $this->_data['configurable_attribute']['data']['fields']['option[value][option_1][0]']['value'] = 'option2';
    }
}
