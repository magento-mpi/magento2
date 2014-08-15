<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Handler\CustomerSegment;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Magento\Backend\Test\Handler\Conditions;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;

/**
 * Class Curl
 * Curl handler for creating customer segment through backend.
 */
class Curl extends Conditions implements CustomerSegmentInterface
{
    /**
     * Map of type parameter
     *
     * @var array
     */
    protected $mapTypeParams = [
        'Conditions combination' => [
            'type' => 'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root',
            'aggregator' => 'all',
            'value' => 1
        ],
        'Default Billing Address' => [
            'type' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes',
            'attribute' => 'default_billing'
        ],
        'Default Shipping Address' => [
            'type' => 'Magento\CustomerSegment\Model\Segment\Condition\Customer\Attributes',
            'attribute' => 'default_shipping'
        ]
    ];

    /**
     * Map of rule parameters
     *
     * @var array
     */
    protected $mapRuleParams = [
        'value' => [
            'exists' => 'is_exists',
        ],
    ];

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
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
        $url = $_ENV['app_backend_url'] . 'customersegment/index/save/back/edit/active_tab/general_section';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $data = $this->replaceMappingData($customerSegment->getData());

        if ($customerSegment->hasData('conditions_serialized')) {
            $data['rule']['conditions'] = $this->prepareCondition($data['conditions_serialized']);
            unset($data['conditions_serialized']);
        }

        $data['website_ids'] = $this->getWebsiteIdsValue($data['website_ids']);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception(
                "CustomerSegment entity creating by curl handler was not successful!" . " Response: $response"
            );
        }

        return ['segment_id' => $this->getCustomerSegmentId($response)];
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
        $result = [];

        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        $names = is_array($names) ? $names : [$names];
        foreach ($names as $name) {
            preg_match(
                '/<a[^>]+href="[^"]+website_id\\/([\\d]+)\\/"[^>]*>' . preg_quote($name) . '<\\/a>/',
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
     * Get customer segment id from response
     *
     * @param string $response
     * @return int|null
     */
    protected function getCustomerSegmentId($response)
    {
        preg_match('/customersegment\/index\/delete\/id\/([0-9]+)/', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }
}
