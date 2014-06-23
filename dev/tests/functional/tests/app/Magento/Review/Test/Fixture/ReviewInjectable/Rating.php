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
     * Data of the created ratings
     *
     * @var array
     */
    protected $data;

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
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
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
     * @param string|null $key
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
