<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

use Magento\Store\Test\Fixture\Website;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class WebsiteId
 * Prepare data for website_id field in reward rate fixture
 *
 * Data keys:
 *  - dataSet
 */
class WebsiteId implements FixtureInterface
{
    /**
     * Website name
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
     * Website fixture
     *
     * @var Website
     */
    protected $website;

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
            /** @var Website $website */
            $website = $fixtureFactory->createByCode('website', ['dataSet' => $data['dataSet']]);
            if (!$website->hasData('website_id')) {
                $website->persist();
            }
            $this->website = $website;
            $this->data = $website->getName();
        }
    }

    /**
     * Persist custom website
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
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return website fixture
     *
     * @return Website
     */
    public function getWebsite()
    {
        return $this->website;
    }
}
