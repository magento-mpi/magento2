<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

class CustomerSegment
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
        $url = $_ENV['app_backend_url'] . 'customersegment/index/save/active_tab/general_section';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();

        if (preg_match_all("/customersegment\/index\/edit\/id\/([0-9]+)/", $response, $matches)) {
            $lastSegmentId = $matches[1][count($matches[1])-1];
        }
        $segmentId =  isset($lastSegmentId) ? $lastSegmentId : null;

        return $segmentId;
    }
}
