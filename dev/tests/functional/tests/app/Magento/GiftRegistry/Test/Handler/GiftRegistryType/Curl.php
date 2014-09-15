<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Handler\GiftRegistryType;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating Gift Registry Type
 */
class Curl extends AbstractCurl implements GiftRegistryTypeInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'admin/giftregistry/save/store/0/back/edit/active_tab/general_section/';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'is_listed' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'group' => [
            'Event Information' => 'event_information',
            'Gift Registry Properties' => 'registry',
            'Privacy Settings' => 'privacy',
            'Recipients Information' => 'registrant',
            'Shipping Address' => 'shipping',
        ],
        'type' => [
            'Custom Types/Text' => 'text',
            'Custom Types/Select' => 'select',
            'Custom Types/Date' => 'date',
            'Custom Types/Country' => 'country',
            'Static Types/Event Date' => 'event_date',
            'Static Types/Event Country' => 'event_country',
            'Static Types/Event Location' => 'event_location',
            'Static Types/Role' => 'role',
        ],
    ];

    /**
     * POST request for creating gift registry type
     *
     * @param FixtureInterface|null $fixture [optional]
     * @throws \Exception
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Gift registry type creating by curl handler was not successful! Response: $response");
        }

        preg_match('@/delete/id/(\d+)/.*Delete@ms', $response, $matches);
        return ['type_id' => $matches[1]];
    }

    /**
     * Prepare data for CURL request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $preparedData = [];
        foreach ($data as $key => $value) {
            if ($key != 'attributes') {
                $preparedData['type'][$key] = $value;
            } else {
                $preparedData['attributes']['registry'] = $data[$key];
                foreach ($preparedData['attributes']['registry'] as &$attribute) {
                    $attribute = $this->prepareAttributes($attribute);
                }
            }
        }
        return $preparedData;
    }

    /**
     * Preparing attributes array for curl response
     *
     * @param array $attribute
     * @return array
     */
    protected function prepareAttributes(array $attribute)
    {
        $attribute['frontend']['is_required'] = $attribute['is_required'];
        unset ($attribute['is_required']);
        return $attribute;
    }
}
