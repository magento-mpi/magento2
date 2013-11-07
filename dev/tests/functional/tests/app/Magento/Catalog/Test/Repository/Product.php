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
use \Magento\Catalog\Test\Fixture;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Product extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple_required'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple'] = $this->_data['simple_required'];
        $this->_data['simple']['data']['category_name'] = '%category::getCategoryName%';

        $this->_data['simple_with_new_category'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildSimpleWithNewCategoryData($defaultData),
        );
    }

    /**
     * Build data for simple product with new category
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildSimpleWithNewCategoryData($defaultData)
    {
        return array(
            'category_new' => array(
                'category_name' => array(
                    'value' => 'New category %isolation%',
                ),
                'parent_category' => array(
                    'value' => 'Default',
                ),
            ),
            'fields' => array_intersect_key($defaultData['fields'], array_flip(array('name', 'sku', 'price'))),
        );
    }
}
