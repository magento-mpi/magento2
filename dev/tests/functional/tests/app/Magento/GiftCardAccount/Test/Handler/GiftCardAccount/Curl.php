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
     * Create gift card account
     *
     * @param FixtureInterface $fixture
     * @return array|mixed
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        foreach ($data as &$value) {
            switch ($value) {
                case 'Yes':
                    $value = 1;
                    break;
                case 'No':
                    $value = 0;
                    break;
                case 'Main Website':
                    $value = 1;
                    break;
                case '%date%':
                    $value = "01/01/2054";
                default:
                    break;
            }
        }
        $url = $_ENV['app_backend_url'] . 'admin/giftcardaccount/save/active_tab/info/';
        $generateCode = $_ENV['app_backend_url'] . 'admin/giftcardaccount/generate/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $generateCode);
        $curl->read();
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $text = $curl->read();
        preg_match('`<td data-column=\"code\".*?>(.*?)<`mis', $text, $res);
        $curl->close();
        return ['code' => trim($res[1])];
    }
}
