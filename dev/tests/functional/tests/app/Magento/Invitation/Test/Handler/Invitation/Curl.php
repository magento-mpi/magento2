<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Handler\Invitation;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl.
 * Create invitation.
 */
class Curl extends AbstractCurl implements InvitationInterface
{
    /**
     * Data mapping.
     *
     * @var array
     */
    protected $mappingData = [
        'store_id' => [
            'Main Website/Main Website Store/Default Store View' => 1,
        ],
        'group_id' => [
            'General' => 1,
            'Wholesale' => 2,
            'Retailer' => 3,
        ]
    ];

    /**
     * Url to save invitation.
     *
     * @var string
     */
    protected $url = 'invitations/index/save';

    /**
     * Post request for creating invitation.
     *
     * @param FixtureInterface $fixture
     * @throws \Exception
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $data = $this->replaceMappingData($this->prepareData($fixture));
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Invitation creation by curl handler was not successful! Response: $response");
        }
        $curl->close();
    }

    /**
     * Prepare POST data for creating invitation request.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data = $fixture->getData();
        $data['email'] = implode("\n", $data['email']);

        return $data;
    }
}
