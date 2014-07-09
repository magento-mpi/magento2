<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureInterface;

/**
 * Class CheckoutData
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData implements FixtureInterface
{
    /**
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * @constructor
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        $this->data = (isset($data['value']) && $data['value'] != '-') ? $data['value'] : null;
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
     * @param int $key [optional]
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
     * Return array preset
     *
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
