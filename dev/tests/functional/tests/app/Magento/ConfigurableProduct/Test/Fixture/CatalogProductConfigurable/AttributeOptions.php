<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class AttributeOptions
 * Source attributes of the configurable products
 */
class AttributeOptions implements FixtureInterface
{
    /**
     * Data set resource
     *
     * @var array
     */
    protected $data = [];

    /**
     * Isolation for unique names
     */
    const ISOLATION_PLACEHOLDER = '%isolation%';

    /**
     * URL conservation attributes
     */
    const SAVE_URL = 'catalog/product_attribute/save/product_tab/variations/store/0/popup/1';

    /**
     * Data placeholder
     *
     * @var array
     */
    protected $placeholderData = [
        'frontend_input' => [
            'Multiple Select' => 'multiselect',
            'Dropdown' => 'select',
        ],
        'is_required' => [
            'Yes' => 1,
            'No' => 0
        ]
    ];

    /**
     * Source constructor
     *
     * @param int $isolation
     * @param array $params
     * @param bool $persist
     * @param array $data
     */
    public function __construct($isolation, array $params, $persist = false, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        }
        $placeholder = self::ISOLATION_PLACEHOLDER;
        array_walk_recursive(
            $this->data,
            function (&$item, $key, $placeholder) use ($isolation) {
                $item = str_replace($placeholder, $isolation, $item);
            },
            $placeholder
        );

        if ($persist && empty($this->data['id'])) {
            $this->persist();
        }
    }

    /**
     * Persist product attribute
     *
     * @return void
     * @throws \Exception
     */
    public function persist()
    {
        $curlData = $this->replacePlaceholder($this->data);

        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $_ENV['app_backend_url'] . self::SAVE_URL, '1.0', array(), $curlData);
        $response = $curl->read();
        $curl->close();

        if (strpos($response, 'data-ui-id="messages-message-success"') === false) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        $matches = [];
        preg_match('#.*\(function\s+\(\$\)\s+\{\s+var\s+data\s*=\s*([^\;]+)\;.*#umi', $response, $matches);

        $result = json_decode($matches[1], true);
        $curlData['id'] = $result['attribute']['id'];
        foreach ($result['attribute']['options'] as $key => $value) {
            if (isset($curlData['option']['value']['option_' . $key])) {
                $curlData['option']['value']['option_' . $key]['id'] = $value['value'];
            }
        }

        $this->data = $curlData;
    }

    /**
     * Replace placeholder data in source data
     *
     * @param array $data
     * @return array
     */
    protected function replacePlaceholder(array $data)
    {
        array_walk_recursive(
            $data,
            function (&$item, $key, $placeholder) {
                $item = isset($placeholder[$key][$item]) ? $placeholder[$key][$item] : $item;
            },
            $this->placeholderData
        );
        return $data;
    }

    /**
     * Return prepared data set
     *
     * @param $key
     * @return mixed
     */
    public function getData($key = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Getting preset data
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'MAGETWO-23263' => [
                'attribute_options' => [
                    'attribute_label' => 'test%isolation%',
                    'frontend_input' => 'Dropdown',
                    'is_required' => 'No',
                    'options' => [
                        'option[value][option_0][0]' => 'option 0',
                        'option[value][option_1][0]' => 'option 1',
                    ]
                ]
            ],
            'default' => [
                'frontend_label' => 'frontend_attribute_label_%isolation%',
                'attribute_code' => 'attribute_code_%isolation%',
                'frontend_input' => 'Dropdown',
                'is_required' => 'No',
                'default' => 'option_0',
                'option' => [
                    'order' => [
                        'option_0' => 1,
                        'option_1' => 2
                    ],
                    'value' => [
                        'option_0' => [
                            'option_0_admin_label_%isolation%',
                            'option_0_store_view_label_%isolation%'
                        ],
                        'option_1' => [
                            'option_1_admin_label_%isolation%',
                            'option_1_store_view_label_%isolation%'
                        ]
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
