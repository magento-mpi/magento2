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
    /** @var array Data */
    protected $data;

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
            $dataSets = explode(',', $data['dataSet']);
            foreach ($dataSets as $dataSet) {
                $taxClassRepository = $fixtureFactory->createByCode('taxClass', ['dataSet' => $dataSet]);
                if (!$taxClassRepository->getClassId()) {
                    $taxClassRepository->persist();
                }
                $this->data = $taxClassRepository->getClassName();
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
}
