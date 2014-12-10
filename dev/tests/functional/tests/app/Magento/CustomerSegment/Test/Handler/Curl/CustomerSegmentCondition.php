<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

class CustomerSegmentCondition
{
    /**
     * Prepare POST data for creating customer request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function _prepareData(FixtureInterface $fixture)
    {
        $data = $fixture->getData('fields');
        foreach ($data as $key => $values) {
            $value = $this->_getValue($values);
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
    protected function _getValue($values)
    {
        if (!isset($values['value'])) {
            return null;
        }
        return isset($values['input_value']) ? $values['input_value'] : $values['value'];
    }

    /**
     * Post request for creating customer in backend
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $params = $this->_prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'customersegment/index/save/active_tab/conditions_section';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $params);
        $response = $curl->read();
        $curl->close();

        preg_match("/customersegment\/index\/edit\/id\/([0-9]+)/", $response, $matches);
        $segmentId =  isset($matches[1]) ? $matches[1] : null;

        return $segmentId;
    }
}
