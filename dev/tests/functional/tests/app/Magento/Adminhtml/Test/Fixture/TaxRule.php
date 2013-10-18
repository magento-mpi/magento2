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
            'tax_rule'          => array(
                'config' => array(
                    'constraint' => 'Success'
                ),
                'data' => array(
                    'fields' => array(
                        'code'   => array(
                            'value' => 'Tax Rule %isolation%'
                        ),
                        'tax_rate'    => array(
                            'value' => '1',
                            'curl' => 'tax_rate[]'
                        ),
                        'tax_product_class'  => array(
                            'value' => '2',
                            'curl' => 'tax_product_class[]'
                        ),
                        'tax_customer_class' => array(
                            'value' => '3',
                            'curl' => 'tax_customer_class[]'
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
        $this->switchData('tax_rule');
    }

    /**
     * Returns data for curl POST params
     *
     * @return array
     */
    public function getCurlPostParams()
    {
        $fields = $this->getData('fields');
        $params = array();
        foreach ($fields as $fieldId => $fieldData) {
            $params[isset($fieldData['curl']) ? $fieldData['curl'] : $fieldId] = $fieldData['value'];
        }
        return $params;
    }
}
