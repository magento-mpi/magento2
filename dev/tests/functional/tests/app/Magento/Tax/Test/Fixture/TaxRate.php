<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class TaxRate
 *
 * @package Magento\Tax\Test\Fixture
 */
class TaxRate extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Tax\Test\Repository\TaxRate';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Tax\Test\Handler\TaxRate\TaxRateInterface';

    protected $defaultDataSet = [
        'class_type' => null,
        'code' => 'Tax Rate %isolation%',
        'rate' => '10',
        'tax_country_id' => 'United States',
        'tax_postcode' => '*',
        'tax_region_id' => '0',
    ];

    protected $class_id = [
        'attribute_code' => 'class_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $class_name = [
        'attribute_code' => 'class_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $class_type = [
        'attribute_code' => 'class_type',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => 'CUSTOMER',
        'input' => '',
    ];

    protected $code = [
        'attribute_code' => 'code',
    ];

    protected $zip_is_range = [
        'attribute_code' => 'zip_is_range',
    ];

    protected $tax_postcode = [
        'attribute_code' => 'tax_postcode',
    ];

    protected $zip_from = [
        'attribute_code' => 'zip_from',
    ];

    protected $zip_to = [
        'attribute_code' => 'zip_to',
    ];

    protected $tax_country_id = [
        'attribute_code' => 'tax_country_id',
    ];

    protected $tax_region_id = [
        'attribute_code' => 'tax_region_id',
    ];

    protected $rate = [
        'attribute_code' => 'rate',
    ];

    protected $id = [
        'attribute_code' => 'id',
    ];

    public function getClassId()
    {
        return $this->getData('class_id');
    }

    public function getClassName()
    {
        return $this->getData('class_name');
    }

    public function getClassType()
    {
        return $this->getData('class_type');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getZipIsRange()
    {
        return $this->getData('zip_is_range');
    }

    public function getTaxPostcode()
    {
        return $this->getData('tax_postcode');
    }

    public function getZipFrom()
    {
        return $this->getData('zip_from');
    }

    public function getZipTo()
    {
        return $this->getData('zip_to');
    }

    public function getTaxCountryId()
    {
        return $this->getData('tax_country_id');
    }

    public function getTaxRegionId()
    {
        return $this->getData('tax_region_id');
    }

    public function getRate()
    {
        return $this->getData('rate');
    }

    public function getId()
    {
        return $this->getData('id');
    }
}
