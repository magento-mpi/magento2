<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class ApplyConfig
 * Curl handler for persisting Magento configuration
 */
class ApplyConfig extends Curl
{
    /**
     * Post request for each fixture section
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $sections = $fixture->getData()['sections'];
        $fields = ['groups' => []];
        foreach ($sections as $section => $data) {
            $fields['groups'] = $data['groups'];
            $url = $_ENV['app_backend_url'] . 'admin/system_config/save/section/' . $section . '/';
            $curl = new BackendDecorator(new CurlTransport(), new Config());
            $curl->write(CurlInterface::POST, $url, '1.0', [], $fields);
            $curl->read();
            $curl->close();
        }
    }
}
