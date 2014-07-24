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
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Create Currency Symbol Entity
 */
class Curl extends AbstractCurl implements CurrencySymbolEntityInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $url = $_ENV['app_backend_url'] . 'admin/system_currencysymbol/save';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $curl->read();
        $curl->close();
    }
}
