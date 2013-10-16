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

namespace Magento\Adminhtml\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class TaxClass
 *
 * @package Magento\Adminhtml\Test\Fixture
 */
class TaxClass extends DataFixture
{
    /**
     * Get tax class name
     *
     * @return string
     */
    public function getTaxClassName()
    {
        return $this->getData('fields/class_name/value');
    }

    /**
     * Create tax class
     */
    public function persist()
    {
        Factory::getApp()->magentoAdminhtmlCreateTaxClass($this);

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'customer_tax_class'          => array(
                'config' => array(
                    'constraint' => 'Success'
                ),
                'data' => array(
                    'fields' => array(
                        'class_name'   => array(
                            'value' => 'Customer Tax Class %isolation%'
                        ),
                        'class_type'    => array(
                            'value' => 'CUSTOMER'
                        )
                    )
                )
            ),
            'product_tax_class'          => array(
                'config' => array(
                    'constraint' => 'Success'
                ),
                'data' => array(
                    'fields' => array(
                        'class_name'   => array(
                            'value' => 'Customer Tax Class %isolation%'
                        ),
                        'class_type'    => array(
                            'value' => 'PRODUCT'
                        )
                    )
                )
            ),
        );

        //Default data set
        $this->switchData('customer_tax_class');
    }
}
