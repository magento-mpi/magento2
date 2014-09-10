<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

/**
 * Class CouponCode
 * Coupon code data
 */
class CouponCode implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * Constructor
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;
        if (isset($data['value']) && $data['value'] instanceof SalesRuleInjectable) {
            $this->data = $data['value'];
            return;
        }
        if (isset($data['dataSet'])) {
            $salesRule = $fixtureFactory->createByCode('salesRuleInjectable', ['dataSet' => $data['dataSet']]);
            $salesRule->persist();
            $this->data = $salesRule;
        }
    }

    /**
     * Persist order products
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
     * @param string $key [optional]
     * @return mixed
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
}
