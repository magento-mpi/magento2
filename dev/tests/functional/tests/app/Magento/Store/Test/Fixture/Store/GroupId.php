<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Fixture\Store;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Store\Test\Fixture\StoreGroup;

/**
 * Class GroupId
 * Prepare StoreGroup for Store
 */
class GroupId implements FixtureInterface
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
     * StoreGroup fixture
     *
     * @var StoreGroup
     */
    protected $storeGroup;

    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $storeGroup = $fixtureFactory->createByCode('storeGroup', ['dataSet' => $data['dataSet']]);
            /** @var StoreGroup $storeGroup */
            if ($storeGroup->getName() != 'Main Website Store') {
                $storeGroup->persist();
            }
            $this->storeGroup = $storeGroup;
            $this->data = $storeGroup->getWebsiteId() . "/" . $storeGroup->getName();
        }
    }

    /**
     * Persist attribute options
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
     * @param string|null $key [optional]
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

    /**
     * Return StoreGroup fixture
     *
     * @return StoreGroup
     */
    public function getStoreGroup()
    {
        return $this->storeGroup;
    }
}
