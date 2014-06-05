<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductAttribute;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Create new Product Attribute via curl
 */
class Curl extends AbstractCurl implements CatalogProductAttributeInterface
{
    /**
     * Post request for creating Product Attribute
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $frontend_inputs = [
            'Text Field' => 'text',
            'Text Area' => 'textarea',
            'Date' => 'date',
            'Yes/No' => 'boolean',
            'Multiple Select' => 'multiselect',
            'Dropdown' => 'select',
            'Price' => 'price',
            'Media Image' => 'media_image',
            'Fixed Product Tax' => 'weee',
        ];

        $data = $fixture->getData();
        $data['frontend_label'] = [0 => $data['frontend_label']];
        $data['is_required'] = ($data['is_required'] == 'Yes') ? 1 : 0;
        $data['frontend_input'] = $frontend_inputs[$data['frontend_input']];
        $data['option'] = [
            'value' => [
                'option_0' => ['black', 'option_0'],
                'option_1' => ['white', 'option_1'],
                'option_2' => ['green', 'option_2'],
            ],
            'order' => [
                'option_0' => 1,
                'option_1' => 2,
                'option_2' => 3,
            ]
        ];
        $data['default'][] = 'option_0';

        $url = $_ENV['app_backend_url'] . 'catalog/product_attribute/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match(
            '`<tr.*?http.*?attribute_id\/(\d*?)\/`',
            $response,
            $matches
        );
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['id' => $id];
    }
}
