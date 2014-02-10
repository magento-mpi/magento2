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
 * Class Product Attribute Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ProductAttribute extends AbstractRepository
{
    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['configurable_attribute'] = $this->_data['default'];

        $this->_data['new_attribute'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildNewAttributeData($defaultData),
        );

        $this->_data['price_massaction'] = array(
            'data' => array(
                'fields' => array(
                    'price' => array('value' => '1.99', 'group' => 'attributes_update_tabs_attributes')
                )
            )
        );
    }

    /**
     * Build new attribute data set
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildNewAttributeData(array $defaultData)
    {
        unset($defaultData['fields']['is_configurable']);
        unset($defaultData['fields']['attribute_code']);
        return $defaultData;
    }
}
