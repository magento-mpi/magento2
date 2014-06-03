<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Handler\CatalogProductConfigurable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use \Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductSimpleCurl;

/**
 * Class CreateProduct
 * Create new Configurable product via curl
 */
class Curl extends ProductSimpleCurl implements CatalogProductConfigurableInterface
{
    /**
     * @param FixtureInterface $fixture
     * @return array|mixed|string
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $config = $fixture->getDataConfig();
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        if ($fixture instanceof InjectableFixture) {
            $fields = $fixture->getData();
            if ($prefix) {
                $data[$prefix] = $fields;
                foreach($data[$prefix]['configurable_attributes_data'] as $key => $attributeInfo){
                    $data['attributes'][] = $key;
                }
            } else {
                $data = $fields;
            }
        } else {
            $data = $this->_prepareData($fixture->getData('fields'), $prefix);
        }

        if ($fixture->getData('category_id')) {
            $data['product']['category_ids'] = $fixture->getData('category_id');
        }
        $url = $_ENV['app_backend_url'] . 'catalog/product/save/type/configurable/set/4/popup/1/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];
    }
}
