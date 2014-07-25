<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Handler\CurrencySymbolEntity;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Create Currency Symbol Entity
 */
class Curl extends AbstractCurl implements CurrencySymbolEntityInterface
{
    /**
     * Post request for creating currency symbol
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $url = $_ENV['app_backend_url'] . 'admin/system_currencysymbol/save';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $curl->read();
        $curl->close();
        // Response verification is absent, because sending a post request returns an index page
    }
}
