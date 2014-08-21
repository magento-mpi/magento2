<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Fixture\RewardRate;

use Magento\Customer\Test\Fixture\CustomerGroupInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CustomerGroup
 * Prepare data for customer_group_id field in reward fixture
 *
 * Data keys:
 *  - dataSet
 */
class CustomerGroup implements FixtureInterface
{
    /**
     * Customer Group code
     *
     * @var string
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Customer Group fixture
     *
     * @var CustomerGroupInjectable
     */
    protected $customerGroup;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            /** @var CustomerGroupInjectable $customerGroup */
            $customerGroup = $fixtureFactory->createByCode('customerGroupInjectable', ['dataSet' => $data['dataSet']]);
            if (!$customerGroup->hasData('customer_group_id')) {
                $customerGroup->persist();
            }
            $this->customerGroup = $customerGroup;
            $this->data = $customerGroup->getCustomerGroupCode();
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
     * @param string|null $key
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
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return customer group fixture
     *
     * @return CustomerGroupInjectable
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }
}
