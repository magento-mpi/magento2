<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Fixture\TaxRule;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Price
 *
 * Data keys:
 *  - dataSet
 *
 * @package Magento\Tax\Test\Fixture\TaxRule
 */
class TaxRate implements FixtureInterface
{
    /**
     * Array with tax rates codes
     *
     * @var array $data
     */
    protected $data;

    /**
     * Array with tax rate fixtures
     *
     * @var array $taxRate
     */
    protected $taxRate;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $dataSets = $data['dataSet'];
            foreach ($dataSets as $dataSet) {
                if ($dataSet != '-') {
                    /** @var \Magento\Tax\Test\Fixture\TaxRate $taxRate */
                    $taxRate = $fixtureFactory->createByCode('taxRate', ['dataSet' => $dataSet]);
                    $this->taxRate[] = $taxRate;
                    $this->data[] = $taxRate->getCode();
                }
            }
        }
    }

    /**
     * Persist custom selections tax rates
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
     * Return tax rate fixture
     *
     * @return array
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

}
