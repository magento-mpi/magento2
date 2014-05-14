<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class TaxRate
 *
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
        return $this->getData('code/value');
    }

    /**
     * Get tax rate id
     *
     * @return string
     */
    public function getTaxRateId()
    {
        return $this->getData('fields/id');
    }

    /**
     * Create tax rate
     *
     * @return TaxRate
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoTaxCreateTaxRate($this);
        $this->_data['fields']['id'] = $id;
        return $this;
    }

    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'code' => array(
                    'value' => 'Tax Rate %isolation%'
                ),
                'rate' => array(
                    'value' => '10'
                ),
                'tax_country_id' => array(
                    'value' => 'US',
                ),
                'tax_postcode' => array(
                    'value' => '*'
                ),
                'tax_region_id' => array(
                    'value' => '0'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoTaxTaxRate($this->_dataConfig, $this->_data);
    }
}
