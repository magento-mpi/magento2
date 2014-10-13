<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture\BannerInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Class CatalogRules
 * Prepare catalog rules
 */
class CatalogRules implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Return catalog rules
     *
     * @var CatalogRule
     */
    protected $catalogRules = [];

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
                /** @var CatalogRule $catalogRule */
                $catalogRule = $fixtureFactory->createByCode('catalogRule', ['dataSet' => $preset]);
                if (!$catalogRule->getId()) {
                    $catalogRule->persist();
                }

                $this->data[] = $catalogRule->getId();
                $this->catalogRule[] = $catalogRule;
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
     * Return catalog rules fixture
     *
     * @return CatalogRule
     */
    public function getCatalogRules()
    {
        return $this->catalogRules;
    }
}
