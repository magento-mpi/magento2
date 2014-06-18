<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogAttributeSet;

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
class Curl extends AbstractCurl implements CatalogAttributeSetInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'skeleton_set' => [
            'Default' => 4,
        ],

    ];
    /**
     * Post request for creating Attribute Set
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->replaceMappingData($fixture->getData());
        if (!isset($data['gotoEdit'])) {
            $data['gotoEdit'] = 1;
        }

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

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Attribute Set creating by curl handler was not successful!");
        }

        return ['attribute_set_id' => $id];
    }
}
