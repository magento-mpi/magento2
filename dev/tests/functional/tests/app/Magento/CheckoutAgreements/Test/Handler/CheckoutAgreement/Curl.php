<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\Handler\CheckoutAgreement;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements CheckoutAgreementInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Enabled' => 1,
            'Disabled' => 0
        ],
        'is_html' => [
            'HTML' => 1,
            'Text' => 0
        ],
        'store_ids' => [
            'Main Website/Main Website Store/Default Store View' => 1,
        ],
    ];

    /**
     * Url for save checkout agreement
     *
     * @var string
     */
    protected $url = 'checkout/agreement/save/';

    /**
     * Post request for creating new checkout agreement
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $data = $this->prepareData($this->replaceMappingData($fixture->getData()));
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Checkout agreement creating by curl handler was not successful! Response: $response");
        }
        preg_match("~id\/(\d*?)\/~", $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['agreement_id' => $id];
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $data['stores'] = [$data['store_ids']];
        unset($data['store_ids']);

        return $data;
    }
}
