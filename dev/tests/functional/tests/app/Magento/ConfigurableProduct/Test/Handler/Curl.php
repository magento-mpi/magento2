<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Handler;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new configurable product via curl
 */
class Curl extends AbstractCurl implements CatalogProductConfigurableInterface
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
        $this->extendPlaceholder();

        $fields = $this->prepareStockData($this->replacePlaceholder($fixture->getData()));
        $variationsMatrix = $fields['variations_matrix'];
        unset($fields['variations_matrix']);

        if ($fixture->hasData('tax_class_id')) {
            $taxClassId = $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClass()->getId();
            $fields['tax_class_id'] = $taxClassId === null
                ? $this->getTaxClassId($fields['tax_class_id'])
                : $taxClassId;
        }

        $data = $prefix ? [$prefix => $fields] : $fields;
        $data['attributes'] = array_keys($fields['configurable_attributes_data']);
        $data['new-variations-attribute-set-id'] = 4;
        $data['affect_configurable_product_attributes'] = 1;
        $data['variations-matrix'] = $variationsMatrix;

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

    /**
     * Expand basic placeholder
     *
     * @return void
     */
    protected function extendPlaceholder()
    {
        $this->placeholderData['is_percent'] = [
            'No' => 0,
            'Yes' => 1
        ];

        $this->placeholderData['include'] = [
            'Yes' => 1,
            'No' => 0
        ];
    }
}
