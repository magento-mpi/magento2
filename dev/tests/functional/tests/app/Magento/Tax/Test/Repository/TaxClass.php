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

namespace Magento\Tax\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Tax Class Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class TaxClass extends AbstractRepository
{
    function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['customer_tax_class'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['product_tax_class'] = $this->_data['customer_tax_class'];
        $this->_data['product_tax_class']['data']['class_name']['value'] = 'Product Tax Class %isolation%';
        $this->_data['product_tax_class']['data']['class_type']['value'] = 'PRODUCT';
    }
}
