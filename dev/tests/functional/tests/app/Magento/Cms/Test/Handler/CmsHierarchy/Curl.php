<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Handler\CmsHierarchy;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements CmsHierarchyInterface
{
    /**
     * Post request for creating Cms Hierarchy
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $url = $_ENV['app_backend_url'] . 'admin/cms_hierarchy/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Cms page entity creatingdle//////////r was not successful! Response: $response");
        }
        preg_match(
            '/.*node_id":"(\d+).*' . $fixture->getIdentifier() . '/',
            $response,
            $matches
        );
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['id' => $id];
    }
}
