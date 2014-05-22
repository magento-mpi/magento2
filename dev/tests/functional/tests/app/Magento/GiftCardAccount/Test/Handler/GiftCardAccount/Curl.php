<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Handler\GiftCardAccount;

use Magento\GiftCardAccount\Test\Handler\GiftCardAccount\GiftCardAccountInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

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
    protected $dataMapping = ['website_id ' => ['Main Website' => 1]];

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
     * @param FixtureInterface $fixture
     * @return array|mixed
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture->getData());

        $url = $_ENV['app_backend_url'] . $this->activeTabInfo;
        $generateCode = $_ENV['app_backend_url'] . $this->generate;
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $generateCode);
        $curl->read();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $content = $curl->read();
        preg_match('`<td data-column=\"code\".*?>(.*?)<`mis', $content, $res);
        $curl->close();
        return ['code' => trim($res[1])];
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->dataMapping[$key])) {
                $data[$key] = $this->dataMapping[$key][$value];
            } elseif ($value === 'Yes') {
                $data[$key] = 1;
            } elseif ($value === 'No') {
                $data[$key] = 0;
            }
        }
        return $data;
    }
}
