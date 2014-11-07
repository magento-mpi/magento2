<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Handler\GiftCardAccount;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Create gift card account
 */
class Curl extends AbstractCurl implements GiftCardAccountInterface
{
    /**
     * Data mapping
     *
     * @var array
     */
    protected $mappingData = [
        'status' => [
            'Yes' => 1,
            'No' => 1,
        ],
        'is_redeemable' => [
            'Yes' => 1,
            'No' => 1,
        ]
    ];

    /**
     * Active tab info link
     *
     * @var string
     */
    protected $activeTabInfo = 'admin/giftcardaccount/save/active_tab/info/';

    /**
     * Gift card account generate link
     *
     * @var string
     */
    protected $generate = 'admin/giftcardaccount/generate/';

    /**
     * Create gift card account
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->replaceMappingData($fixture->getData());
        $data['website_id'] = $fixture->getDataFieldConfig('website_id')['source']->getWebsite()->getWebsiteId();

        $url = $_ENV['app_backend_url'] . $this->activeTabInfo;
        $generateCode = $_ENV['app_backend_url'] . $this->generate;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $generateCode);
        $curl->read();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $content = $curl->read();

        if (!strpos($content, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $content");
        }

        preg_match('`<td data-column=\"code\".*?>(.*?)<`mis', $content, $res);
        $curl->close();
        return ['code' => trim($res[1])];
    }
}
