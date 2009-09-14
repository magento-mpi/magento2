<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance Model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Widget_Instance extends Mage_Core_Model_Abstract
{
    const SPECIFIC_ENTITIES = 'specific';
    const ALL_ENTITIES      = 'all';

    protected $_pageGroups = array(
        'anchor_categories' => 'catalog_category_layered',
        'notanchor_categories' => 'catalog_category_default',
        'all_pages' => 'default'
    );

    protected $_specificEntitiesLayouHandles = array(
        'anchor_categories' => 'CATEGORY_{{ID}}',
        'notanchor_categories' => 'CATEGORY_{{ID}}',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_cms/widget_instance');
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $this->_pageGroups[$typeId.'_products'] = 'PRODUCT_TYPE_'.$typeId;
            $this->_specificEntitiesLayouHandles[$typeId.'_products'] = 'PRODUCT_{{ID}}';
        }
    }

    /**
     * Processing object before save data
     *
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    protected function _beforeSave()
    {
        $pageGroupIds = array();
        $tmpPageGroups = array();
        $pageGroups = $this->getData('page_groups');
        if ($pageGroups) {
            foreach ($pageGroups as $pageGroup) {
                $tmpPageGroup = array();
                if (isset($pageGroup[$pageGroup['page_group']])) {
                    $pageGroupData = $pageGroup[$pageGroup['page_group']];
                    if ($pageGroupData['page_id']) {
                        $pageGroupIds[] = $pageGroupData['page_id'];
                    }
                    if ($pageGroup['page_group'] == 'pages') {
                        $layoutHandle = $pageGroupData['layout_handle'];
                    } else {
                        $layoutHandle = $this->_pageGroups[$pageGroup['page_group']];
                    }
                    $tmpPageGroup = array(
                        'page_id' => $pageGroupData['page_id'],
                        'group' => $pageGroup['page_group'],
                        'layout_handle' => $layoutHandle,
                        'for' => $pageGroupData['for'],
                        'block_reference' => $pageGroupData['block'],
                        'entities' => '',
                        'position' => $pageGroupData['position']
                    );
                    if ($pageGroupData['for'] == self::SPECIFIC_ENTITIES) {
                        $tmpPageGroup = array_merge($tmpPageGroup, array(
                            'entities' => $pageGroupData['entities'],
                            'specific_layout_handle' => $this->_specificEntitiesLayouHandles[$pageGroup['page_group']]
                        ));
                    }
                    $tmpPageGroups[] = $tmpPageGroup;
                }
            }
        }
        if (is_array($this->getData('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getData('store_ids')));
        }
        if (is_array($this->getData('widget_parameters'))) {
            $this->setData('widget_parameters', serialize($this->getData('widget_parameters')));
        }
        $this->setData('page_groups', $tmpPageGroups);
        $this->setData('page_group_ids', $pageGroupIds);
        return parent::_beforeSave();
    }

    /**
     * Check if widget instance has required data (other data depends on it)
     *
     * @return boolean
     */
    public function isCompleteToCreate()
    {
        return (bool)($this->getType() && $this->getPackageTheme());
    }

    /**
     * Setter
     * Replace '-' to '/', if was passed from request(GET request)
     *
     * @param string $type
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    public function setType($type)
    {
        if (strpos($type, '-')) {
            $type = str_replace('-', '/', $type);
        }
        $this->setData('type', $type);
        return $this;
    }

    /**
     * Getter
     * Replace '-' to '/', if was set from request(GET request)
     *
     * @return string
     */
    public function getType()
    {
        if (strpos($this->_getData('type'), '-')) {
            $this->setData('type', str_replace('-', '/', $this->_getData('type')));
        }
        return $this->_getData('type');
    }

    /**
     * Setter
     * Replace '_' to '/', if was passed from request(GET request)
     *
     * @param string $packageTheme
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    public function setPackageTheme($packageTheme)
    {
        if (strpos($packageTheme, '_')) {
            $packageTheme = str_replace('_', '/', $packageTheme);
        }
        $this->setData('package_theme', $packageTheme);
        return $this;
    }

    /**
     * Getter.
     * Replace '_' to '/', if was set from request(GET request)
     *
     * @return string
     */
    public function getPackageTheme()
    {
        if (strpos($this->_getData('package_theme'), '_')) {
            $this->setData('package_theme', str_replace('_', '/', $this->_getData('package_theme')));
        }
        return $this->_getData('package_theme');
    }

    /**
     * Getter.
     * If not set return default
     *
     * @return string
     */
    public function getArea()
    {
        if (!$this->_getData('area')) {
            return Mage_Core_Model_Design_Package::DEFAULT_AREA;
        }
        return $this->_getData('area');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        if (!$this->_getData('package')) {
            $this->_parsePackageTheme();
        }
        return $this->_getData('package');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        if (!$this->_getData('theme')) {
            $this->_parsePackageTheme();
        }
        return $this->_getData('theme');
    }

    /**
     * Parse packageTheme and set parsed package and theme
     *
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    protected function _parsePackageTheme()
    {
        if ($this->getPackageTheme() && strpos($this->getPackageTheme(), '/')) {
            list($package, $theme) = explode('/', $this->getPackageTheme());
            $this->setData('package', $package);
            $this->setData('theme', $theme);
        }
        return $this;
    }

    /**
     * Getter
     * Explode to array if string setted
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (null !== ($storeIds = $this->getData('store_ids')) && is_string($storeIds)) {
            $this->setData('store_ids', explode(',', $storeIds));
        }
        return $this->getData('store_ids');
    }

    /**
     * Getter
     * Unserialize if serialized string setted
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        if (($widgetParameters = $this->getData('widget_parameters')) && is_string($widgetParameters)) {
            $this->setData('widget_parameters', unserialize($widgetParameters));
        }
        return $this->getData('widget_parameters');
    }
}