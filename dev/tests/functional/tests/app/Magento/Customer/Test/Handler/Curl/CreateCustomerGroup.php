<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating customer group in admin
 *
 * @package Magento\Customer\Test\Handler\Curl
 */
class CreateCustomerGroup extends Curl
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'customer/group/save/';

    /**
     * Prepare POST data for creating customer request
     *
     * @param Fixture $fixture
     * @return array
     */
    protected function prepareData(Fixture $fixture)
    {
        $data = $fixture->getData('fields');
        foreach ($data as $key => $values) {
            $value = $this->getValue($values);
            if (null === $value) {
                continue;
            }
            $data[$key] = $value;
        }

        return $data;
    }


    /**
     * Retrieve field value or return null if value does not exist
     *
     * @param array $values
     * @return null|mixed
     */
    protected function getValue($values)
    {
        if (!isset($values['value'])) {
            return null;
        }
        return isset($values['input_value']) ? $values['input_value'] : $values['value'];
    }

    /**
     * Execute handler
     *
     * @param Fixture $fixture [optional]
     * @return mixed
     */
    public function execute(Fixture $fixture = null)
    {
        $params = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}
