<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Fixture\ReviewInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterfac;
use Magento\Review\Test\Fixture\Rating as FixtureRating;

/**
 * Class Ratings
 * Source for product ratings fixture
 */
class Ratings implements  FixtureInterface
{
    /**
     * Configuration settings of fixture
     *
     * @var array
     */
    protected $params;

    /**
     * Data of the created ratings
     *
     * @var array
     */
    protected $data = [];

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

        foreach ($data as $rating) {
            if (isset($rating['dataSet'])) {
                /** @var FixtureRating $fixtureRating */
                $fixtureRating = $fixtureFactory->createByCode('rating', ['dataSet' => $rating['dataSet']]);
                if (!$fixtureRating->hasData('id')) {
                    $fixtureRating->persist();
                }
                $this->ratings[] = $fixtureRating;

                $this->data[] = [
                    'title' => $fixtureRating->getRatingCode(),
                    'rating' => $rating['rating']
                ];
            }
        }
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
