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
     * Rollback
     *
     * @var bool
     */
    protected $rollback;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $configData
     * @param bool $rollback
     */
    public function __construct(FixtureFactory $fixtureFactory, $configData, $rollback = false)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->configData = $configData;
        $this->rollback = $rollback;
    }

    /**
     * Set config
     *
     * @return array
     */
    public function run()
    {
        if ($this->configData === '-') {
            return [];
        }
        $prefix = ($this->rollback == false) ? '' : '_rollback';

        $configData = array_map('trim', explode(',', $this->configData));
        $result = [];

        foreach ($configData as $configDataSet) {
            $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => $configDataSet . $prefix]);
            $config->persist();

            $result[] = $config;
        }

        return ['config' => $result];
    }
}
