<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Handler\TaxClass;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Add New Tax Class
 *
 * @package Magento\Tax\Test\Handler\TaxClass
 */
class Curl extends AbstractCurl implements TaxClassInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        foreach ($fixture->getData() as $key => $val) {
            $data[$key] = $val;
        }
        $url = $_ENV['app_backend_url'] . 'tax/tax/ajaxSave/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $curl->read();
        $curl->close();
    }
}
