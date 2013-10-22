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
use Mtf\System\Config;

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
     * Contains categories that are needed to create next category
     *
     * @var array
     */
    protected $_categories;

    /**
     * Update to switch data for always applying these placeholders when switching to any data set
     *
     * @param $name
     * @return bool
     */
    public function switchData($name)
    {
        $this->_placeholders['men::getCategoryName'] = array($this, '_categoryProvider');
        $this->_placeholders['men::getCategoryId'] = array($this, '_categoryProvider');

        return parent::switchData($name);
    }

    /**
     * Create category needed for placeholders in data and call method for placeholder
     *
     * @param string $placeholder
     * @return string
     */
    protected function _categoryProvider($placeholder)
    {
        list($key, $method) = explode('::', $placeholder);
        if (!isset($this->_categories[$key])) {
            $this->_categories[$key] = Factory::getFixtureFactory()->getMagentoCatalogCategory();
            $this->_categories[$key]->switchData($key);
            $this->_categories[$key]->persist();
        }

        return is_callable(array($this->_categories[$key], $method)) ? $this->_categories[$key]->$method() : '';
    }

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
     * Get product name
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->getData('category_path/input_value');
    }

    /**
     * Create category
     *
     * @return Category
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoCatalogCreateCategory($this);
        $this->_data['category_path']['input_value'] = $id;
        return $this;
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'request_params' => array(
                'store' => '0'
            ),
            'input_prefix' => 'general'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogCategory($this->_dataConfig, $this->_data);
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
        $params[] = 'parent/' . $this->getData('fields/path')['input_value'];
        return implode('/', $params);
    }

    /**
     * Get path where to create new category
     *
     * @return string
     */
    public function getCategoryPath()
    {
        return $this->getData('category_path/value');
    }
}
