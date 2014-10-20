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
use Magento\Store\Test\Fixture\Store;

/**
 * Class Email
 * Store email source
 */
class Email implements FixtureInterface
{
    /**
     * Resource data
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
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param string $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data)
    {
        $this->params = $params;
        $emails = explode(',', $data);
        $data = [];
        foreach ($emails as $key => $value) {
            $data[$key + 1] = trim($value);
        }
        $this->data = $data;
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
     * @param string|null $key [optional]
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
}
