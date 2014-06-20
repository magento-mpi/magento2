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
use Magento\CustomerSegment\Test\Fixture\CustomerSegment as CustomerSegmentFixture;

/**
 * Class CustomerSegment
 * Prepare customer segment
 */
class CustomerSegment implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Return customer segment
     *
     * @var CustomerSegmentFixture
     */
    protected $customerSegment = [];

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if ($data['dataSet'] && $data['dataSet'] != "-") {
            $dataSet = explode(',', $data['dataSet']);
            foreach ($dataSet as $customerSegment) {
                /** @var CustomerSegmentFixture $segment */
                $segment = $fixtureFactory->createByCode('customerSegment', ['dataSet' => $customerSegment]);
                $segment->persist();
                $this->customerSegment[] = $segment;
                $this->data[] = $segment->getName();
            }
        } else {
            $this->data[] = null;
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
     * Return entity
     *
     * @return CustomerSegmentFixture
     */
    public function getCustomerSegments()
    {
        return $this->customerSegment;
    }
}
