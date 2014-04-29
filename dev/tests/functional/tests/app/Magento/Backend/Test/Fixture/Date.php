<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Fixture;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Date
 *
 * Data keys:
 *  - pattern (Format a local time/date with delta, e.g. 'm-d-Y -3 days' = current day - 3 days)
 *
 * @package Magento\Catalog\Test\Fixture\CatalogProductSimple
 */
class Date implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $params
     * @param array $data
     * @throws \Exception
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['pattern']) && $data['pattern'] !== '-') {
            $matches = [];
            $delta = '';
            if (preg_match_all('/(\+|-)\d+.+/', $data['pattern'], $matches)) {
                $delta = $matches[0][0];
            }
            $timestamp = $delta === '' ? time() : strtotime($delta);
            if (!$timestamp) {
                throw new \Exception('Invalid date format for "' . $this->params['attribute_code'] . '" field');
            }
            $date = date(str_replace($delta, '', $data['pattern']), $timestamp);
            if (!$date) {
                $date = date('m/d/Y');
            }
            $this->data = $date;
        }
    }

    /**
     * Persists prepared data into application
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
     * @param $key [optional]
     * @return mixed
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
}