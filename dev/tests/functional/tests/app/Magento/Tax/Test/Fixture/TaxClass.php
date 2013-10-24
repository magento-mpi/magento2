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

/**
 * Class TaxClass
 *
 * @package Magento\Tax\Test\Fixture
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
     *
     * @return TaxClass
     */
    public function persist()
    {
        Factory::getApp()->magentoTaxCreateTaxClass($this);
        return $this;
    }

    /**
     * Init data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'class_name' => array(
                    'value' => 'Customer Tax Class %isolation%'
                ),
                'class_type' => array(
                    'value' => 'CUSTOMER'
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoTaxTaxClass($this->_dataConfig, $this->_data);
    }
}
