<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture\GiftCardProduct;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\Price as ParentPrice;

/**
 * Class Price
 *
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 *
 */
class Price extends ParentPrice implements FixtureInterface
{
    /**
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'price_from-120' => [
                'compare_price' => [
                    'price_from' => '120.00',
                ]
            ],
        ];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
