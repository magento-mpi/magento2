<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
 * Curl handler for persisting Magento configuration
 *
 * @package Magento\Core\Test\Handler\Curl
 */
class ApplyConfig extends Curl
{
    /**
     * Post request for each fixture section
     * @param FixtureInterface $fixture
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $sections = $fixture->getData()['sections'];
        $fields = array('groups' => array());
        foreach ($sections as $section => $data) {
            $fields['groups'] = $data['groups'];
            $url = $_ENV['app_backend_url'] . 'admin/system_config_save/index/section/' . $section . '/';
            $curl = new BackendDecorator(new CurlTransport(), new Config());
            $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
            $response = $curl->read();
            $curl->close();
        }
    }
}