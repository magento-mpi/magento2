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

namespace Magento\Catalog\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Category
 *
 * @package Magento\Catalog\Test\Fixture
 */
class Category extends DataFixture
{
    /**
     * Attribute set for mapping data into ui tabs
     */
    const GROUP_GENERAL_INFORMATION = 'category_info_tabs_group_4';

    /**
     * Get product name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getData('fields/name/value');
    }

    /**
     * Create category
     *
     * @return Category
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateCategory($this);
        return $this;
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'subcategory' => array(
                'config' => array(
                    'constraint' => 'Success',
                    'request_params' => array(
                        'store' => '0',
                        'parent' => '2'
                    )
                ),
                'data' => array(
                    'fields' => array(
                        'name' => array(
                            'value' => 'Subcategory %isolation%',
                            'group' => static::GROUP_GENERAL_INFORMATION,
                            'curl'  => 'general[name]'
                        ),
                        'is_active' => array(
                            'value' => 'Yes',
                            'group' => static::GROUP_GENERAL_INFORMATION,
                            'input' => 'select',
                            'curl'  => 'general[is_active]'
                        ),
                        'include_in_menu' => array(
                            'value' => 'Yes',
                            'group' => static::GROUP_GENERAL_INFORMATION,
                            'input' => 'select',
                            'curl'  => 'general[include_in_menu]'
                        )
                    ),
                    'category_path' => 'Default Category (0)/some1 (0)/Subcategory 122997687 (0)'
                )
            ),
        );

        //Default data set
        $this->switchData('subcategory');
    }

    /**
     * Returns data for curl POST params
     *
     * @return array
     */
    public function getPostParams()
    {
        $fields = $this->getData('fields');
        $params = array();
        foreach ($fields as $fieldId => $fieldData) {
            $params[isset($fieldData['curl']) ? $fieldData['curl'] : $fieldId] = $fieldData['value'];
        }
        return $params;
    }

    /**
     * Get Url params
     *
     * @param string $urlKey
     * @return string
     */
    public function getUrlParams($urlKey)
    {
        $params = array();
        $config = $this->getDataConfig();
        if (!empty($config[$urlKey]) && is_array($config[$urlKey])) {
            foreach ($config[$urlKey] as $key => $value) {
                $params[] = $key .'/' .$value;
            }
        }
        return implode('/', $params);
    }

    /**
     * Get path where to create new category
     *
     * @return string
     */
    public function getCategoryPath()
    {
        return $this->getData('category_path');
    }
}
