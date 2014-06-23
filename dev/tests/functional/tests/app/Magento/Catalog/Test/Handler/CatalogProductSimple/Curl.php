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
     * Placeholder for data sent Curl
     *
     * @var array
     */
    protected $placeholderData = [
        'manage_stock' => [
            'Yes' => 1,
            'No' => 0
        ],
        'is_virtual' => [
            'Yes' => 1
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
        ],
    ];

    /**
     * Placeholder for price data sent Curl
     *
     * @var array
     */
    protected $priceData = [
        'website' => [
            'name' => 'website_id',
            'data' => [
                'All Websites [USD]' => 0
            ]
        ],
        'customer_group' => [
            'name' => 'cust_group',
            'data' => [
                'ALL GROUPS' => 32000,
                'NOT LOGGED IN' => 0
            ]
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
        // @todo remove "if" when fixtures refactored
        if ($fixture instanceof InjectableFixture) {
            $fields = $this->replacePlaceholder($fixture->getData(), $this->placeholderData);
            // Getting Tax class id
            if ($fixture->hasData('tax_class_id')) {
                $taxClassId = $fixture->getDataFieldConfig('tax_class_id')['source']->getTaxClass()->getId();
                $fields['tax_class_id'] = ($taxClassId === null)
                    ? $this->getTaxClassId($fields['tax_class_id'])
                    : $taxClassId;
            }
            if (isset($fields['tier_price'])) {
                $fields['tier_price'] = $this->preparePriceData($fields['tier_price']);
            }
            if (isset($fields['group_price'])) {
                $fields['group_price'] = $this->preparePriceData($fields['group_price']);
            }
            if (!empty($fields['category_ids'])) {
                $categoryIds = [];
                foreach ($fields['category_ids'] as $categoryData) {
                    $categoryIds[] = $categoryData['id'];
                }
                $fields['category_ids'] = $categoryIds;
            }

            $data = $prefix ? [$prefix => $fields] : $fields;
        } else {
            $data = $this->_prepareData($fixture->getData('fields'), $prefix);
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
     * Replace placeholder data in fixture data
     *
     * @param array $data
     * @param array $placeholders
     * @return array
     */
    private function replacePlaceholder(array $data, array $placeholders)
    {
        foreach ($data as $key => $value) {
            if (!isset($placeholders[$key])) {
                continue;
            }
            if (is_array($value)) {
                $data[$key] = $this->replacePlaceholderValues($value, $placeholders[$key]);
            } else {
                $data[$key] = isset($placeholders[$key][$value]) ? $placeholders[$key][$value] : $value;
            }
        }
        return $data;
    }

    /**
     * Replace placeholder data in fixture values
     *
     * @param array $data
     * @param array $placeholders
     * @return array
     */
    private function replacePlaceholderValues(array $data, array $placeholders)
    {
        foreach ($data as $key => $value) {
            if (isset($placeholders[$value])) {
                $data[$key] = $placeholders[$value];
            }
        }
        return $data;
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
     * @param array $params
     * @param string|null $prefix
     * @return array
     */
    protected function _prepareData($params, $prefix = null)
    {
        $data = array();
        foreach ($params as $key => $values) {
            $value = $this->_getValue($values);
            //do not add this data if value does not exist
            if (null === $value) {
                continue;
            }
            if (isset($values['input_name'])) {
                $data[$values['input_name']] = $value;
            } elseif ($prefix) {
                $data[$prefix][$key] = $value;
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Preparation of tier price data
     *
     * @param array $fields
     * @return array
     */
    protected function preparePriceData(array $fields)
    {
        foreach ($fields as &$field) {
            foreach ($this->priceData as $key => $data) {
                $field[$data['name']] = $this->priceData[$key]['data'][$field[$key]];
                unset($field[$key]);
            }
            $field['delete'] = '';
        }
        return $fields;
    }

    /**
     * Retrieve field value or return null if value does not exist
     *
     * @param array $values
     * @return null|mixed
     */
    protected function _getValue($values)
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
    protected function _getUrl(array $config)
    {
        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }
        return $_ENV['app_backend_url'] . 'catalog/product/save/' . $params . 'popup/1/back/edit';
    }
}
