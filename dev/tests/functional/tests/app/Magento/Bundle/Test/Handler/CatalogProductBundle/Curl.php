<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Handler\CatalogProductBundle;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;

/**
 * Class Curl
 * Create new bundle product via curl
 */
class Curl extends ProductCurl implements CatalogProductBundleInterface
{
    /**
     * Constructor
     *
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        parent::__construct($configuration);

        $this->mappingData += [
            'selection_can_change_qty' => [
                'Yes' => 1,
                'No' => 0
            ],
            'sku_type' => [
                'Dynamic' => 0,
                'Fixed' => 1
            ],
            'price_type' => [
                'Dynamic' => 0,
                'Fixed' => 1
            ],
            'weight_type' => [
                'Dynamic' => 0,
                'Fixed' => 1
            ],
            'shipment_type' => [
                'Together' => 0,
                'Separately' => 1
            ],
            'type' => [
                'Drop-down' => 'select',
                'Radio Buttons' => 'radio',
                'Checkbox' => 'checkbox',
                'Multiple Select' => 'multi',
            ],
            'selection_price_type' => [
                'Fixed' => 0,
                'Percent' => 1
            ]
        ];
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
        $data = parent::prepareData($fixture, null);

        $selections = [];
        $bundleSelections = [];
        if (!empty($data['bundle_selections'])) {
            $selections = $data['bundle_selections'];
            $products = $selections['products'];
            unset($data['selections'], $selections['products']);

            foreach ($selections['bundle_options'] as $key => &$option) {
                $option['delete'] = '';
                $option['position'] = $key;
                foreach ($option['assigned_products'] as $productKey => $assignedProduct) {
                    $assignedProduct['data'] += [
                        'product_id' => $products[$key][$productKey]->getId(),
                        'delete' => '',
                        'position' => $productKey
                    ];
                    $bundleSelections[$key][] = $assignedProduct['data'];
                }
                unset($option['assigned_products']);
            }
        }
        $data = $prefix ? [$prefix => $data] : $data;
        $data = array_merge($data, $selections);
        $data['bundle_selections'] = $bundleSelections;

        return $this->replaceMappingData($data);
    }
}
