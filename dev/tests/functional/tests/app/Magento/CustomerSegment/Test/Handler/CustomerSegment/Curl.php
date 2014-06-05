<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Handler\CustomerSegment;

use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating target rule through backend.
 */
class Curl extends AbstractCurl implements CustomerSegmentInterface
{
    /**
     * Mapping values
     *
     * @var array
     */
    protected $mapping = [
        'is_active' => [
            'Active' => 1,
            'Inactive' => 0,
        ],
        'apply_to' => [
            'Visitors and Registered Customers' => 0,
            'Registered Customers' => 1,
            'Visitors' => 2,
        ],
    ];

    /**
     * Post request for creating customer segment in backend
     *
     * @param FixtureInterface|null $customerSegment
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $customerSegment = null)
    {
        /** @var CustomerSegment $customerSegment */
        $url = $_ENV['app_backend_url'] . 'customersegment/index/save/edit/active_tab/general_section';
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $data = $customerSegment->getData();
        $data['is_active'] = $this->getIsActiveValue($data['is_active']);
        $data['apply_to'] = $this->getApplyToValue($data['apply_to']);
        $data['website_ids'] = $this->getWebsiteIdsValue($data['website_ids']);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("CustomerSegment entity creating by curl handler was not successful!" .
                " Response: $response");
        }

        return ['id' => $this->getCustomerSegmentId($customerSegment->getName())];
    }

    /**
     * Get "is_active" value by label
     *
     * @param string $label
     * @return int
     * @throws \Exception
     */
    protected function getIsActiveValue($label)
    {
        if (!isset($this->mapping['is_active'][$label])) {
            throw new \Exception("Unidentified value \"{$label}\" for field \"Is Active\"");
        }
        return $this->mapping['is_active'][$label];
    }

    /**
     * Get "apply_to" value by label
     *
     * @param string $label
     * @return int
     * @throws \Exception
     */
    protected function  getApplyToValue($label)
    {
        if (!isset($this->mapping['apply_to'][$label])) {
            throw new \Exception("Unidentified value \"{$label}\" for field \"Apply To\"");
        }
        return $this->mapping['apply_to'][$label];
    }

    /**
     * Get "website_ids" values by names
     *
     * @param array|string $names
     * @return array
     * @throws \Exception
     */
    protected function getWebsiteIdsValue($names)
    {
        $url = $_ENV['app_backend_url'] . 'admin/system_store';
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        $result = [];
        foreach ($names as $name) {
            preg_match(
                '/<a[^>]+href="[^"]+website_id\\/([\\d]+)\\/"[^>]*>' . $name . '<\\/a>/',
                $response,
                $match
            );
            if (!isset($match[1])) {
                throw new \Exception("Can't find website id by name \"{$name}\". Response: $response");
            }

            $result[] = $match[1];
        }

        return $result;
    }

    /**
     * Get customer segment id by name
     *
     * @param string $name
     * @return int|null
     */
    protected function getCustomerSegmentId($name)
    {
        $filter = ['name' => $name];
        $url = $_ENV['app_backend_url'] . 'customersegment/index/grid/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="entity_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
