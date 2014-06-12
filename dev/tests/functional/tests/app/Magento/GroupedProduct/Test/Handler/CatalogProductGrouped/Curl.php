<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Handler\CatalogProductGrouped;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new grouped product via curl
 */
class Curl extends AbstractCurl implements CatalogProductGroupedInterface
{
    /**
     * Post request for creating configurable product
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $config = $fixture->getDataConfig();
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $fields = $this->prepareStockData($this->replacePlaceholder($fixture->getData()));

        if ($fixture->hasData('tax_class_id')) {
            $taxClassId = $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClass()->getId();
            $fields['tax_class_id'] = $taxClassId === null
                ? $this->getTaxClassId($fields['tax_class_id'])
                : $taxClassId;
        }
        $relatedProducts = $fields['grouped_products']['products'];
        unset($fields['grouped_products']);

        $data = $prefix ? [$prefix => $fields] : $fields;

        $data['links']['associated'] = [];
        $links = & $data['links']['associated'];
        foreach ($relatedProducts as $key => $product) {
            /** @var FixtureInterface $product */
            $relatedProductData = $product->getData();
            $links[$relatedProductData['id']]['id'] = $relatedProductData['id'];
            $links[$relatedProductData['id']]['position'] = $key + 1;
            $links[$relatedProductData['id']]['qty'] = $relatedProductData['qty'];
        }

        $url = $this->_getUrl($config);
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
