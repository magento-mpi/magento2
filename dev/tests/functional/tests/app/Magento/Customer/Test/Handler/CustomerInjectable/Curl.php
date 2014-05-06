<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerInjectable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 *
 * @package Magento\Customer\Test\Handler\CustomerInjectable
 */
class Curl extends AbstractCurl implements CustomerInjectableInterface
{
    /**
     * Post request for creating customer in backend
     *
     * @param FixtureInterface|null $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'customer/index/save/back/edit/active_tab/account/';
        $data = $this->prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Customer entity creating  by curl handler was not successful! Response: $response");
        }

        return ['id' => $this->getId($response)];
    }

    /**
     * Prepare POST data for creating customer request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data = ['account' => []];

        foreach ($fixture->getData() as $key => $value) {
            if (null !== $value) {
                $data['account'][$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Get customer id from response
     *
     * @param string $response
     * @return int|null
     */
    protected function getId($response)
    {
        $match = null;
        preg_match('/id="id"[^<>]+value="(\d+)"/', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }
}
