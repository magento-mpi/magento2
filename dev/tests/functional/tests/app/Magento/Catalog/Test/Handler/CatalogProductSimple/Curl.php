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
        'links_purchased_separately' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_shareable' => [
            'Yes' => 1,
            'No' => 0,
            'Use config' => 2
        ],
        'required' => [
            'Yes' => 1,
            'No' => 0
        ],
        'manage_stock' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_virtual' => [
            'Yes' => 1
        ],
        'use_config_enable_qty_increments' => [
            'Yes' => 1,
            'No' => 0
        ],
        'use_config_qty_increments' => [
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
        'tax_class_id' => [
            'None' => 0,
            'Taxable Goods' => 2
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
     * @param FixtureInterface|null $fixture [optional]
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
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
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

        
        // Getting Attribute Set id
        if ($fixture->hasData('attribute_set_id')) {
            $attributeSetId = $fixture
                ->getDataFieldConfig('attribute_set_id')['source']
                ->getAttributeSet()
                ->getAttributeSetId();
            $fields['attribute_set_id'] = $attributeSetId;
        }

        $fields = $this->prepareStockData($fields);

        $data = $prefix ? [$prefix => $fields] : $fields;

        return $data;
    }

    /**
     * Preparation of stock data
     *
     * @param array $fields
     * @return array
     */
    protected function prepareStockData(array $fields)
    {
        if (!is_array($fields['quantity_and_stock_status'])) {
            $fields['quantity_and_stock_status'] = [
                'qty' => $fields['qty'],
                'is_in_stock' => $fields['quantity_and_stock_status']
            ];
        }

        if (!isset($fields['stock_data']['is_in_stock'])) {
            $fields['stock_data']['is_in_stock'] = isset($fields['quantity_and_stock_status']['is_in_stock'])
                ? $fields['quantity_and_stock_status']['is_in_stock']
                : (isset($fields['inventory_manage_stock']) ? $fields['inventory_manage_stock'] : null);
        }
        if (!isset($fields['stock_data']['qty'])) {
            $fields['stock_data']['qty'] = isset($fields['quantity_and_stock_status']['qty'])
                ? $fields['quantity_and_stock_status']['qty']
                : null;
        }

        if (!isset($fields['stock_data']['manage_stock'])) {
            $fields['stock_data']['manage_stock'] = (int)(!empty($fields['stock_data']['qty'])
                || !empty($fields['stock_data']['is_in_stock']));
        }

        return $this->filter($fields);
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

    /**
     * Replace mapping data in fixture data
     *
     * @param array $data
     * @return array
     */
    protected function replaceMappingData(array $data)
    {
        $mapping = $this->mappingData;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replaceMappingData($value);
            } else {
                if (!isset($mapping[$key])) {
                    continue;
                }
                $data[$key] = isset($mapping[$key][$value]) ? $mapping[$key][$value] : $value;
            }
        }

        return $data;
    }
}
