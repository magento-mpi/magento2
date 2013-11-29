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
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        
        $this->_data['configurable_required'] = $this->_data['default'];
        $this->_data['configurable'] = $this->_data['default'];
        $this->_data['configurable']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['configurable']['data']['affect_configurable_product_attributes'] = 'Template %isolation%';
        $this->_data['configurable_advanced_pricing'] = $this->getConfigurableAdvancedPricing();
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
                        'group' => 'product_info_tabs_advanced-pricing'
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['configurable'], $pricing);

        return $product;
    }
}
