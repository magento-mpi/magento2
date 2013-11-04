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

namespace Magento\Tax\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\System\Config;

/**
 * Class TaxRule
 *
 * @package Magento\Tax\Test\Fixture
 */
class TaxRule extends DataFixture
{
    /**
     * Initialize data and apply placeholders
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, array $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['us_ca_rate_8_25'] = array($this, '_getTaxRateId');
        $this->_placeholders['us_ny_rate_8_1'] = array($this, '_getTaxRateData');
        $this->_placeholders['us_ny_rate_8_375'] = array($this, '_getTaxRateId');
        $this->_placeholders['product_tax_class'] = array($this, '_getTaxClassId');
        $this->_placeholders['customer_tax_class'] = array($this, '_getTaxClassId');
    }

    /**
     * Callback function returns created rate id
     *
     * @param string $dataSetName
     * @return int
     */
    protected function _getTaxRateId($dataSetName)
    {
        $taxRate = Factory::getFixtureFactory()->getMagentoTaxTaxRate();
        $taxRate->switchData($dataSetName);
        return $taxRate->persist()->getTaxRateId();
    }

    /**
     * Callback function returns class id
     *
     * @param string $dataSetName
     * @return mixed
     */
    protected function _getTaxClassId($dataSetName)
    {
        $taxClass = Factory::getFixtureFactory()->getMagentoTaxTaxClass();
        $taxClass->switchData($dataSetName);
        return $taxClass->persist()->getTaxClassId();
    }

    protected function _getTaxRateData($dataSetName)
    {
        $taxClass = Factory::getFixtureFactory()->getMagentoTaxTaxRate();
        $taxClass->switchData($dataSetName);
        return $taxClass->getData('fields');
    }

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
     * Get tax customer class
     *
     * @return string|array
     */
    public function getTaxCustomerClass()
    {
        return $this->getData('fields/tax_customer_class/value');
    }

    /**
     * Get tax product class
     *
     * @return string|array
     */
    public function getTaxProductClass()
    {
        return $this->getData('fields/tax_product_class/value');
    }

    /**
     * Create tax rule
     *
     * @return TaxRule
     */
    public function persist()
    {
        Factory::getApp()->magentoTaxCreateTaxRule($this);
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
                    'value' => 'Tax Rule %isolation%'
                ),
                'tax_rate' => array(
                    'value' => '1',
                    'input_name' => 'tax_rate[]'
                ),
                'tax_product_class' => array(
                    'value' => '2',
                    'input_name' => 'tax_product_class[]'
                ),
                'tax_customer_class' => array(
                    'value' => '3',
                    'input_name' => 'tax_customer_class[]'
                ),
                'priority' => array(
                    'value' => '0'
                ),
                'position' => array(
                    'value' => '0'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()->getMagentoTaxTaxRule($this->_dataConfig, $this->_data);
    }
}
