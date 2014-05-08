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
    /**
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        foreach ($fixture->getData() as $key => $value) {
            $data[$key] = $value;
        }

        $url = $_ENV['app_backend_url'] . 'tax/tax/ajaxSave/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, '"success":true')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match('`"class_id":"(\d+)"`', $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['class_id' => $id];
    }
}
