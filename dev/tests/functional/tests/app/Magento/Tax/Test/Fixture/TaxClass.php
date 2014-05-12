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
 * Class TaxClass
 *
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
        return $this->getData('fields/data/class_name/value');
    }

    /**
     * Return saved class id
     *
     * @return mixed
     */
    public function getTaxClassId()
    {
        return $this->getData('fields/id');
    }

    /**
     * Create tax class
     *
     * @return TaxClass
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoTaxCreateTaxClass($this);
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
