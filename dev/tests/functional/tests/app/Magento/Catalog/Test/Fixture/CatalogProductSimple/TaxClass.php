<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class TaxClass
 *
 * Data keys:
 *  - dataSet
 *  - tax_product_class
 */
class TaxClass implements FixtureInterface
{
    /**
     * Tax class name
     *
     * @var string
     */
    protected $data;

    /**
     * Tax class fixture
     *
     * @var \Magento\Tax\Test\Fixture\TaxClass
     */
    protected $taxClass;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        /** @var \Magento\Tax\Test\Fixture\TaxClass $taxClass */
        if (isset($data['dataSet']) && $data['dataSet'] !== '-') {
            $taxClass = $fixtureFactory->createByCode('taxClass', ['dataSet' => $data['dataSet']]);
            $this->taxClass = $taxClass;
            $this->data = $taxClass->getClassName();
        }
        if (isset($data['tax_product_class'])
            && $data['tax_product_class'] instanceof \Magento\Tax\Test\Fixture\TaxClass
        ) {
            $taxClass = $data['tax_product_class'];
            $this->taxClass = $taxClass;
            $this->data = $taxClass->getClassName();
        }
    }

    /**
     * Persist custom selections tax classes
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return tax class fixture
     *
     * @return \Magento\Tax\Test\Fixture\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }
}
