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
 * @package Magento\Tax\Test\Repository
 */
class TaxClass extends AbstractRepository
{
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
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
        $this->_data['product_tax_class']['data']['fields']['class_name']['value'] = 'Product Tax Class %isolation%';
        $this->_data['product_tax_class']['data']['fields']['class_type']['value'] = 'PRODUCT';
    }
}
