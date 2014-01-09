<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Magento\Catalog\Test\Fixture;
use Magento\Catalog\Test\Fixture\ConfigurableProduct as ConfigurableProductFixture;

/**
 * Class Configurable Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ConfigurableProduct extends Product
{
    const CONFIGURABLE = 'configurable';

    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data[self::CONFIGURABLE]['data']['affect_configurable_product_attributes'] = 'Template %isolation%';
        $this->_data['configurable_advanced_pricing'] = $this->getConfigurableAdvancedPricing();

        $this->_data['product_variations'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildProductVariations($defaultData),
        );

        $this->_data['configurable_checkout_selection_option_label_2'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildConfigurableCheckoutSelectionOptionLabel2($defaultData)
        );
    }

    /**
     * Build configurable product with the second option selected.
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildConfigurableCheckoutSelectionOptionLabel2(array $defaultData)
    {
        return array_merge($defaultData, array(
            'checkout' => array(
                'selections' => array(
                    '0' => array(
                        'attribute_name' => '%attribute_1_name%',
                        'option_name' => '%attribute_1_option_label_2%'
                    )
                )
            )
        ));
    }

    /**
     * Get configurable product with advanced pricing
     *
     * @return array
     */
    protected function getConfigurableAdvancedPricing()
    {
        $pricing = array(
            'data' => array(
                'fields' => array(
                    'special_price' => array(
                        'value' => '9',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data[self::CONFIGURABLE], $pricing);

        return $product;
    }

    /**
     * Build product variations data set
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildProductVariations(array $defaultData)
    {
        $data = $defaultData;
        $data['affect_configurable_product_attributes'] = 'Template %isolation%';
        $data['fields'] = array(
            'configurable_attributes_data' => array(
                'value' => array(
                    '0' => array(
                        'label' => array(
                            'value' => '%new_attribute_label%'
                        ),
                        '0' => array(
                            'option_label' => array(
                                'value' => '%new_attribute_option_1_label%',
                            ),
                            'include' => array(
                                'value' => 'Yes',
                            ),
                        ),
                        '1' => array(
                            'option_label' => array(
                                'value' => '%new_attribute_option_2_label%',
                            ),
                            'include' => array(
                                'value' => 'Yes',
                            ),
                        ),
                    ),
                ),
                'group' => ConfigurableProductFixture::GROUP,
            ),
            'variations-matrix' => array(
                'value' => array(
                    '0' => array(
                        'configurable_attribute' => array(
                            '0' => array(
                                'attribute_option' => '%new_attribute_option_1_label%',
                            ),
                        ),
                        'value' => array(
                            'qty' => array(
                                'value' => 100,
                            ),
                        ),
                    ),
                    '1' => array(
                        'configurable_attribute' => array(
                            '0' => array(
                                'attribute_option' => '%new_attribute_option_2_label%',
                            ),
                        ),
                        'value' => array(
                            'qty' => array(
                                'value' => 100,
                            ),
                        ),
                    ),
                ),
                'group' => ConfigurableProductFixture::GROUP,
            ),
        );
        return $data;
    }
}
