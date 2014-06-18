<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Handler\TaxRate;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating Tax Rate
 */
class Curl extends AbstractCurl implements TaxRateInterface
{
    /**
     * Mapping for countries
     *
     * @var array
     */
    protected $countryId = [
        'AU' => 'Australia',
        'US' => 'United States',
        'GB' => 'United Kingdom',
    ];

    /**
     * Mapping for regions
     *
     * @var array
     */
    protected $regionId = [
        '0' => '*',
        '12' => 'California',
        '43' => 'New York',
    ];

    /**
     * Post request for creating tax rate
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $data['tax_country_id'] = array_search($data['tax_country_id'], $this->countryId);
        if (isset($data['tax_region_id'])) {
            $data['tax_region_id'] = array_search($data['tax_region_id'], $this->regionId);
        } else {
            $data['tax_region_id'] = 0;
        }

        $url = $_ENV['app_backend_url'] . 'tax/rate/ajaxSave/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        $id = $this->getTaxRateId($response);
        return ['id' => $id];
    }

    /**
     * Return saved tax rate id
     *
     * @param $response
     * @return int|null
     * @throws \Exception
     */
    protected function getTaxRateId($response)
    {
        $data = json_decode($response, true);
        if ($data['success'] !== true) {
            throw new \Exception("Tax rate creation by curl handler was not successful! Response: $response");
        }
        return isset($data['tax_calculation_rate_id']) ? (int)$data['tax_calculation_rate_id'] : null;
    }
}
