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
 * Class TaxRule
 *
 * @package Magento\Adminhtml\Test\Fixture
 */
class TaxRule extends DataFixture
{
    /**
     * Get tax rule name
     *
     * @return string
     */
    public function getTaxRuleName()
    {
        return $this->getData('fields/code/value');
    }

    /**
     * Create tax rule
     */
    public function persist()
    {
        Factory::getApp()->magentoAdminhtmlCreateTaxRule($this);

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'tax rule'          => array(
                'config' => array(
                    'constraint' => 'Success'
                ),
                'data' => array(
                    'fields' => array(
                        'code'   => array(
                            'value' => 'Tax Rule %isolation%'
                        ),
                        'tax_rate'    => array(
                            'value' => '1'
                        ),
                        'tax_product_class'  => array(
                            'value' => '2',
                        ),
                        'tax_customer_class' => array(
                            'value' => '3'
                        ),
                        'priority'    => array(
                            'value' => '0'
                        ),
                        'position' => array(
                            'value' => '0'
                        )
                    )
                )
            ),
        );

        //Default data set
        $this->switchData('tax rule');
    }
}
