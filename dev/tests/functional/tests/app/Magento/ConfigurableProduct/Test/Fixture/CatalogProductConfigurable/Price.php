<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Price
 *
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 */
class Price implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * Constructor
     *
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['value'])) {
            $this->data = $data['value'];
        }
        if (isset($data['preset'])) {
            $this->currentPreset = $data['preset'];
        }
    }

    /**
     * Persist custom selections products
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
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
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'MAGETWO-23062' => [
                'category_price' => '100.00',
                'product_price' => '100.00',
                'cart_price' => '154.00'
            ],
            'MAGETWO-23063' => [
                'category_price' => '100.00',
                'product_price' => '100.00',
                'cart_price' => '140.00'
            ],
        ];

        if (!isset($presets[$this->currentPreset])) {
            return null;
        }

        return $presets[$this->currentPreset];
    }
}
