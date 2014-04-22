<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture\CustomerGroup;

use Magento\Tax\Test\Fixture\TaxClassInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class TaxClass
 * @package Magento\Customer\Test\Fixture
 */
class TaxClassIds implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var TaxClassInjectable
     */
    protected $taxClass;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     * @param TaxClassInjectable $taxClass
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data,
        TaxClassInjectable $taxClass
    ) {
        $this->fixtureFactory = $fixtureFactory;

        $this->params = $params;
        if (isset($data['preset']) && $data['preset'] !== '-') {

            $presets = explode(',', $data['preset']);
            foreach ($presets as $preset) {
                $isPreset = $this->fixtureFactory->createByCode('taxClassInjectable', ['dataSet' => $preset]);
                if ($isPreset->getData('class_id')) {
                    $this->data = $isPreset->getData('class_name');
                } else {
                    $taxClass->persist();
                    $this->data = $taxClass->getData('class_name');
                }
            }
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
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Retrieve source category fixture
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
} 