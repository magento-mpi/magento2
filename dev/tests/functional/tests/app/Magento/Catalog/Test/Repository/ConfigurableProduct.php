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

use Mtf\Repository\AbstractRepository;

/**
 * Class Configurable Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ConfigurableProduct extends AbstractRepository
{
    function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['configurable'] = $this->_data['default'];
        $this->_data['configurable'] = array_replace_recursive($this->_data['configurable'], $this->_getConfigurable());
    }

    protected function _getConfigurable()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'Configurable Product %isolation%',
                    ),
                    'sku' => array(
                        'value' => 'configurable_sku_%isolation%',
                    ),
                    'price' => array(
                        'value' => '10',
                    ),
                    'tax_class_id' => array(
                        'value' => 'Taxable Goods',
                    ),
                    'weight' => array(
                        'value' => '1',
                    ),
                    'configurable_attributes_data' => array(
                        'value' => array(
                            '0' => array(
                                'label' => array(
                                    'value' => '%attribute_label_1%'
                                ),
                                '0' => array(
                                    'option_label' => array(
                                        'value' => '%attribute_1_option_label_1%'
                                    ),
                                    'pricing_value' => array(
                                        'value' => '1'
                                    ),
                                    'is_percent' => array(
                                        'value' => 'No'
                                    ),
                                    'include' => array(
                                        'value' => 'Yes'
                                    ),
                                ),
                                '1' => array(
                                    'option_label' => array(
                                        'value' => '%attribute_1_option_label_2%'
                                    ),
                                    'pricing_value' => array(
                                        'value' => '2'
                                    ),
                                    'is_percent' => array(
                                        'value' => 'No'
                                    ),
                                    'include' => array(
                                        'value' => 'Yes'
                                    ),
                                )
                            )
                        )
                    ),
                    'variations-matrix' => array(
                        'value' => array(
                            '0' => array(
                                'configurable_attribute' => array(
                                    '0' => array(
                                        'attribute_label' => '%attribute_code_1%',
                                        'attribute_option' => '%attribute_1_option_label_1%'
                                    )
                                ),
                                'value' => array(
                                    'name' => array(
                                        'value' => 'Variation 0-%isolation%'
                                    ),
                                    'sku' => array(
                                        'value' => 'Variation 0-%isolation%'
                                    ),
                                    'qty' => array(
                                        'value' => 100
                                    )
                                )
                            ),
                            '1' => array(
                                'configurable_attribute' => array(
                                    '0' => array(
                                        'attribute_label' => '%attribute_code_1%',
                                        'attribute_option' => '%attribute_1_option_label_2%'
                                    )
                                ),
                                'value' => array(
                                    'name' => array(
                                        'value' => 'Variation 1-%isolation%'
                                    ),
                                    'sku' => array(
                                        'value' => 'Variation 1-%isolation%'
                                    ),
                                    'qty' => array(
                                        'value' => 200
                                    )
                                )
                            )
                        )
                    )
                ),
                'category_name' => '%category%',
                'affect_configurable_product_attributes' => 'Template %isolation%'
            )
        );
    }
}
