<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Handler\GiftWrapping;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating gift wrapping
 */
class Curl extends AbstractCurl implements GiftWrappingInterface
{
    /**
     * Mapping for websites
     *
     * @var array
     */
    protected $websites = [
        1 => 'Main Website',
    ];

    /**
     * Post request for creating gift wrapping
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);

        $url = $_ENV['app_backend_url'] . 'admin/giftwrapping/save/store/0/back/edit/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Gift Wrapping creation by curl handler was not successful! Response: $response");
        }

        preg_match("~Location: [^\\s]*giftwrapping\\/edit\\/id\\/(\\d+)~", $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['wrapping_id' => $id];
    }

    /**
     * Preparing data
     *
     * @param \Magento\GiftWrapping\Test\Fixture\GiftWrapping $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data['wrapping'] = [
            'design' => $fixture->getDesign(),
            'base_price' => $fixture->getBasePrice(),
            'status' => ($fixture->getStatus() === 'Enabled') ? 1 : 0,
        ];
        foreach ($fixture->getWebsiteIds() as $website) {
            $data['wrapping']['website_ids'][] = array_search($website, $this->websites);
        }

        return $data;
    }
}
