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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog category flat collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Store id of application
     *
     * @var integer
     */
    protected $_storeId = null;

    protected function _construct()
    {
        $this->_init('catalog/category_flat');
        $this->setModel('catalog/category');
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(
            array('main_table' => $this->getResource()->getMainStoreTable($this->getStoreId()))
        );
        return $this;
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id.
     * If store id is not set yet, return store of application
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Add filter by path to collection
     *
     * @param string $parent
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
     */
    public function addParentPathFilter($parent)
    {
        $this->addFieldToFilter('path', array('like' => "{$parent}/%"));
        return $this;
    }

    /**
     * Add store filter
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
     */
    public function addStoreFilter()
    {
        $this->addFieldToFilter('store_id', $this->getStoreId());
        return $this;
    }

    /**
     * Set field to sort by
     *
     * @param string $sorted
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection
     */
    public function addSortedField($sorted)
    {
        if (is_string($sorted)) {
            $this->addOrder($sorted, 'ASC');
        } else {
            $this->addOrder('name', 'ASC');
        }
        return $this;
    }

    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        return $this;
    }

    public function addNameToResult()
    {
        return $this;
    }

    public function addUrlRewriteToResult()
    {
        $storeId = Mage::app()->getStore()->getId();
        $this->getSelect()->joinLeft(
            array('url_rewrite' => $this->getTable('core/url_rewrite')),
            'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND url_rewrite.product_id IS NULL AND url_rewrite.store_id="'.$storeId.'" AND url_rewrite.id_path LIKE "category/%"',
            array('request_path')
        );
        return $this;
    }

    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $select = $this->getSelect();
        $orWhere = false;
        foreach ($paths as $path) {
            if ($orWhere) {
                $select->orWhere('path LIKE ?', "$path%");
            } else {
                $select->where('path LIKE ?', "$path%");
                $orWhere = true;
            }
        }
        return $this;
    }

    public function addLevelFilter($level)
    {
        $this->getSelect()->where('level <= ?', $level);
        return $this;
    }

    public function addOrderField($field)
    {
        $this->setOrder($field, 'ASC');
        return $this;
    }
}