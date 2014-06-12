<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Handler;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class CreateProduct
 * Create new bundle product via curl
 */
class Curl extends AbstractCurl implements CatalogProductBundleInterface
{
    /**
     * Specific array
     *
     * @var array
     */
    protected $specificData = [
        'bundle_selection',
        'bundle_option',
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
        $this->extendPlaceholder();
        // @todo remove "if" when fixtures refactored
        if ($fixture instanceof InjectableFixture) {
            $fields = $this->prepareStockData($this->replacePlaceholder($fixture->getData()));
            $fields = $this->prepareStockData($fields);
            if (!empty($fields['category_ids'])) {
                $categoryIds = [];
                foreach ($fields['category_ids'] as $categoryData) {
                    $categoryIds[] = $categoryData['id'];
                }
                $fields['category_ids'] = $categoryIds;
            }
            $data = $prefix ? [$prefix => $fields] : $fields;

            foreach ($this->specificData as $specific) {
                if (isset($fields[$specific])) {
                    unset($data[$prefix][$specific]);
                    $field = ($specific == 'bundle_selection' || $specific == 'bundle_option')
                        ? $this->prepareBundle($fields[$specific])
                        : $fields[$specific];
                    $data[$specific . 's'] = $field;
                }
            }
        } else {
            $data = $this->_prepareData($fixture->getData('fields'), $prefix);
        }

        if (!empty($data['bundle_selections']) && !empty($fields['bundle_selections']['products'])) {
            $products = $fields['bundle_selections']['products'];
            array_walk_recursive(
                $data['bundle_selections'],
                function (&$item, $key) use (&$products) {
                    if (!empty($products)) {
                        $product = array_pop($products);
                        $item = $key === 'product_id' ? $product->getId() : $item;
                    }
                }
            );
        }
        unset($data['product']['bundle_selections']);

        if ($fixture->getData('category_id')) {
            $data['product']['category_ids'] = $fixture->getData('category_id');
            unset($data['product']['category_id']);
        }
        $url = $this->_getUrl($config);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
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
        $this->placeholderData['new_variations_attribute_set_id'] = [
            'Default' => 4
        ];
        $this->placeholderData['links_purchased_separately'] = [
            'Yes' => 1,
            'No' => 0
        ];
        $this->placeholderData['is_shareable'] = [
            'Yes' => 1,
            'No' => 0,
            'Use config' => 2
        ];
        $this->placeholderData['use_config_enable_qty_increments'] = [
            'Yes' => 1,
            'No' => 0,
        ];
        $this->placeholderData['required'] = [
            'Yes' => 1,
            'No' => 0,
        ];
        $this->placeholderData['use_config_qty_increments'] = [
            'Yes' => 1,
            'No' => 0,
        ];
        $this->placeholderData['selection_can_change_qty'] = [
            'Yes' => 1,
            'No' => 0,
        ];
        $this->placeholderData['sku_type'] = [
            'Dynamic' => 0,
            'Fixed' => 1,
        ];
        $this->placeholderData['price_type'] = [
            'Dynamic' => 0,
            'Fixed' => 1,
        ];
        $this->placeholderData['weight_type'] = [
            'Dynamic' => 0,
            'Fixed' => 1,
        ];
        $this->placeholderData['shipment_type'] = [
            'Together' => 0,
            'Separately' => 1,
        ];
        $this->placeholderData['type'] = [
            'Drop-down' => 'select',
            'Radio Buttons' => 'radio',
            'Checkbox' => 'checkbox',
            'Multiple Select' => 'multi',
        ];
    }

    /**
     * Prepare bundle selection and option data
     *
     * @param array $fields
     * @return array
     */
    protected function prepareBundle(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->prepareBundle($value);
            } else {
                $fields['delete'] = '';
                break;
            }
        }

        return $fields;
    }
}
