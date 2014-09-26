<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class Price
 * Data keys:
 *  - preset (Price data verification preset name)
 */
class Price extends \Magento\Catalog\Test\Fixture\CatalogProductSimple\Price
{
    /**
     * Constructor
     *
     * @constructor
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset();
        }
    }

    /**
     * Get preset array
     *
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'default_with_discount' => [
                'subtotal' => 1120,
                'discount' => 560,
            ],
            'full_invoice' => [
                'grand_order_total' => [565],
                'grand_invoice_total' => [565],
            ],
            'partial_invoice' => [
                'grand_order_total' => [210],
                'grand_invoice_total' => [110]
            ],
        ];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
