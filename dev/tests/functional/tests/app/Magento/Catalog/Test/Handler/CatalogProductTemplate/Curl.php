<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductTemplate;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Create new Attribute Set via curl
 */
class Curl extends AbstractCurl implements CatalogProductTemplateInterface
{
    /**
     * Post request for creating Attribute Set
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData();
        $data['gotoEdit'] = 1;
        $data['skeleton_set'] = 4;

        $url = $_ENV['app_backend_url'] . 'catalog/product_set/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match(
            '`http.*?id\/(\d*?)\/.*?data-ui-id=\"page-actions-toolbar-delete-button\".*`',
            $response,
            $matches
        );
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['id' => $id];
    }
}
