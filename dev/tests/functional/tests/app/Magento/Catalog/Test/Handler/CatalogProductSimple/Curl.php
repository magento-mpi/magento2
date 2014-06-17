<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductSimple;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class CreateProduct
 * Create new simple product via curl
 */
class Curl extends AbstractCurl implements CatalogProductSimpleInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'manage_stock' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_virtual' => [
            'Yes' => 1
        ],
        'inventory_manage_stock' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_in_stock' => [
            'In Stock' => 1,
            'Out of Stock' => 0
        ],
        'visibility' => [
            'Not Visible Individually' => 1,
            'Catalog' => 2,
            'Search' => 3,
            'Catalog, Search' => 4
        ],
        'website_ids' => [
            'Main Website' => 1
        ],
        'status' => [
            'Product offline' => 2,
            'Product online' => 1
        ],
        'attribute_set_id' => [
            'Default' => 4
        ]
    ];

    /**
     * Post request for creating simple product
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $config = $fixture->getDataConfig();
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $data = $this->prepareData($fixture, $prefix);

        return ['id' => $this->createProduct($data, $config)];
    }

    /**
     * Getting tax class id from tax rule page
     *
     * @param string $taxClassName
     * @return int
     * @throws \Exception
     */
    protected function getTaxClassId($taxClassName)
    {
        $url = $_ENV['app_backend_url'] . 'tax/rule/new/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), array());
        $response = $curl->read();
        $curl->close();

        preg_match('~<option value="(\d+)".*>' . $taxClassName . '</option>~', $response, $matches);
        if (!isset($matches[1]) || empty($matches[1])) {
            throw new \Exception('Product tax class id ' . $taxClassName . ' undefined!');
        }

        return (int)$matches[1];
    }

    /**
     * Remove items from a null
     *
     * @param array $data
     * @return array
     */
    protected function filter(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            } elseif (is_array($data[$key])) {
                $data[$key] = $this->filter($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $fields = $this->replaceMappingData($fixture->getData());
        // Getting Tax class id
        if ($fixture->hasData('tax_class_id')) {
            $taxClassId = $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClass()->getId();
            $fields['tax_class_id'] = ($taxClassId === null)
                ? $this->getTaxClassId($fields['tax_class_id'])
                : $taxClassId;
        }

        if (!empty($fields['category_ids'])) {
            $categoryIds = [];
            foreach ($fields['category_ids'] as $categoryData) {
                $categoryIds[] = $categoryData['id'];
            }
            $fields['category_ids'] = $categoryIds;
        }

        if (!empty($fields['website_ids'])) {
            foreach ($fields['website_ids'] as &$value) {
                $value = isset($this->mappingData['website_ids'][$value])
                    ? $this->mappingData['website_ids'][$value]
                    : $value;
            }
        }

        $data = $prefix ? [$prefix => $fields] : $fields;

        return $data;
    }

    /**
     * Create product via curl
     *
     * @param array $data
     * @param array $config
     * @return int|null
     * @throws \Exception
     */
    protected function createProduct(array $data, array $config)
    {
        $url = $this->getUrl($config);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }

    /**
     * Retrieve field value or return null if value does not exist
     *
     * @param array $values
     * @return null|mixed
     */
    protected function getValue($values)
    {
        if (!isset($values['value'])) {
            return null;
        }
        return isset($values['input_value']) ? $values['input_value'] : $values['value'];
    }

    /**
     * Retrieve URL for request with all necessary parameters
     *
     * @param array $config
     * @return string
     */
    protected function getUrl(array $config)
    {
        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }
        return $_ENV['app_backend_url'] . 'catalog/product/save/' . $params . 'popup/1/back/edit';
    }
}
