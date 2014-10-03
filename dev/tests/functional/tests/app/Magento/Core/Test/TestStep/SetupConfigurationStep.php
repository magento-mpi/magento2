<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Core\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Mtf\Fixture\FixtureFactory;

/**
 * Class SetupConfigurationStep
 * Setup configuration using handler
 */
class SetupConfigurationStep implements TestStepInterface
{
    /**
     * Factory for Fixtures
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Configuration data
     *
     * @var string
     */
    protected $configData;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $configData
     */
    public function __construct(FixtureFactory $fixtureFactory, $configData)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->configData = $configData;
    }

    /**
     * Set config
     *
     * @return array
     */
    public function run()
    {
        $configData = array_map('trim', explode(',', $this->configData));
        $result = [];

        foreach ($configData as $configDataSet) {
            $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => $configDataSet]);
            $config->persist();

            $result[] = $config;
        }

        return ['config' => $result];
    }
}
