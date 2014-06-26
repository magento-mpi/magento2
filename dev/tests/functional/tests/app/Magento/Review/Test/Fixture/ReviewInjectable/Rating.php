<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Fixture\ReviewInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\Review\Test\Fixture\Rating as FixtureRating;

/**
 * Class Rating
 * Source for product rating fixture
 */
class Rating extends InjectableFixture
{
    /**
     * Configuration settings of fixture
     *
     * @var array
     */
    protected $params;

    /**
     * List of the created ratings
     *
     * @var array
     */
    protected $ratings = [];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;

        foreach ($data as &$rating) {
            if (isset($rating['dataSet'])) {
                /** @var FixtureRating $fixtureRating */
                $fixtureRating = $fixtureFactory->createByCode('rating', ['dataSet' => $rating['dataSet']]);
                $fixtureRating->persist();

                unset($rating['dataSet']);
                $rating['title'] = $fixtureRating->getRatingCode();

                $this->ratings[] = $fixtureRating;
            }
        }
        $this->data = $data;
    }

    /**
     * Persist data
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
     * @return array
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
     * Get ratings
     *
     * @return array
     */
    public function getRatings()
    {
        return $this->ratings;
    }
}
