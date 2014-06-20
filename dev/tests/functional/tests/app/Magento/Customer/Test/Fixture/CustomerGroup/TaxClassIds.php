<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture\CustomerGroup;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class TaxClass
 * @package Magento\Customer\Test\Fixture
 */
class TaxClassIds implements FixtureInterface
{
    /** @var string Data */
    protected $data;

    /**
     * @var \Magento\Tax\Test\Fixture\TaxClass
     */
    protected $taxClass;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data
    ) {
        $this->params = $params;
        if (isset($data['dataSet']) && $data['dataSet'] !== '-') {
            $dataSet = $data['dataSet'];
            /** @var \Magento\Tax\Test\Fixture\TaxClass $taxClass */
            $taxClass = $fixtureFactory->createByCode('taxClass', ['dataSet' => $dataSet]);
            if (!$taxClass->hasData('id')) {
                $taxClass->persist();
            }
            $this->data = $taxClass->getClassName();
            $this->taxClass = $taxClass;
        }
    }

    /**
     * Persist custom selections products
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
     * @return \Magento\Tax\Test\Fixture\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
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
}
