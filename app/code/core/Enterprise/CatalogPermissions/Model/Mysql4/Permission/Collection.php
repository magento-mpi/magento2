<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Permission collection
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Field mapper
     *
     * @var array
     */
    protected $_map;

    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('enterprise_catalogpermissions/permission');
    }

    /**
     * Set scope filter for permissions collection
     *
     * @param int $customerGroupId
     * @param int|string|array|Varien_Object $category
     * @param int|string|Varien_Object $website
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection
     */
    public function setScopeFilter($customerGroupId = null, $category = null, $website = null)
    {
        if ($customerGroupId !== null) {
           $this->addFieldToFilter('customer_group_id', $customerGroupId);
        }

        if ($category !== null) {
            if (is_int($category)) {
                $this->addFieldToFilter('category_id', $category);
            } elseif (is_array($category)) {
                $this->addFieldToFilter('category_id', array('in'=>$category));
            } elseif (is_string($category)) {
                $category = explode('/', $category);
                $this->addFieldToFilter('category_id', array('in'=>$category));
            } elseif ($category instanceof Varien_Object) {
                $this->addFieldToFilter('category_id', $category->getId());
            }
        }

        $websiteId = Mage::app()->getWebsite($website)->getId();

        $this->addFieldToFilter('website_id', $websiteId);

        return $this;
    }

    /**
     * Add category level to collection
     *
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection
     */
    public function addCategoryLevel()
    {
        if (!isset($this->_map['fields']['level'])) {
            $this->getSelect()
                ->join(array('category'=>$this->getTable('catalog/category')),
                       'category.entity_id = main_table.category_id',
                        'level');
            $this->_map['fields']['level'] = 'category.level';
        }

        return $this;
    }
}