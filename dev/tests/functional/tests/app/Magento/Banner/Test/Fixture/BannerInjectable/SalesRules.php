<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture\BannerInjectable;

use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Class SalesRules
 * Prepare sales rules
 */
class SalesRules implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Return sales rules
     *
     * @var CatalogRule
     */
    protected $salesRules = [];

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['presets']) && $data['presets'] !== '-') {
            $presets = explode(',', $data['presets']);
            foreach ($presets as $preset) {
                /** @var SalesRuleInjectable $salesRules */
                $salesRules = $fixtureFactory->createByCode('salesRuleInjectable', ['dataSet' => $preset]);
                if (!$salesRules->getRuleId()) {
                    $salesRules->persist();
                }

                $this->data[] = $salesRules->getId();
                $this->salesRules[] = $salesRules;
            }
        } else {
            $this->data[] = $data;
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
     * Return prepared data
     *
     * @param string|null $key
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return sales rules fixture
     *
     * @return CatalogRule
     */
    public function getSalesRules()
    {
        return $this->salesRules;
    }
}
