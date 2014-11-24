<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Fixture\Invitation;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Customer\Test\Fixture\CustomerGroupInjectable;

/**
 * Class GroupId.
 * GroupId source for invitation fixture.
 */
class GroupId implements FixtureInterface
{
    /**
     * Source data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Source parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * Customer Group fixture.
     *
     * @var array
     */
    protected $customerGroupFixture;

    /**
     * Source constructor.
     *
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
            $this->data = $customerGroup->getCustomerGroupCode();
            $this->customerGroupFixture = $customerGroup;
        }
        if (isset($data['customerGroup']) && $data['customerGroup'] instanceof CustomerGroupInjectable) {
            $this->data = $data['customerGroup']->getCustomerGroupCode();
            $this->customerGroupFixture = $data['customerGroup'];
        }
    }

    /**
     * Persists prepared data into application.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param int|null $key [optional]
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Getting customerGroup fixture.
     *
     * @return array
     */
    public function getCustomerGroup()
    {
        return $this->customerGroupFixture;
    }
}
