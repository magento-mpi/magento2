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
 * Class TaxRate
 *
 * @package Magento\Adminhtml\Test\Fixture
 */
class TaxRate extends DataFixture
{
    /**
     * Get tax rate name
     *
     * @return string
     */
    public function getTaxRateName()
    {
        return $this->getData('fields/code/value');
    }

    /**
     * Create tax rate
     */
    public function persist()
    {
        Factory::getApp()->magentoAdminhtmlCreateTaxRate($this);

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'tax_rate'          => array(
                'config' => array(
                    'constraint' => 'Success'
                ),
                'data' => array(
                    'fields' => array(
                        'code'   => array(
                            'value' => 'Tax Rate %isolation%'
                        ),
                        'rate'    => array(
                            'value' => '10'
                        ),
                        'tax_country_id'  => array(
                            'value' => 'US',
                        ),
                        'tax_postcode' => array(
                            'value' => '*'
                        ),
                        'tax_region_id'    => array(
                            'value' => '0'
                        )
                    )
                )
            ),
        );

        //Default data set
        $this->switchData('tax_rate');
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
