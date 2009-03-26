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
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Event homepage block
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Event_Home extends Enterprise_CatalogEvent_Block_Event_Abstract
{
    /**
     * Categories with events
     *
     * @var array
     */
    protected $_categories = null;

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return count($this->getCategories()) > 0;
    }

    /**
     * Retreive categories with events
     *
     * @return array
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            $this->_categories = array();
            $categories = $this->helper('catalog/category')->getStoreCategories('position', true, false);
            $categories->addAttributeToSelect('image');
            $allIds = $categories->getAllIds();
            if (!empty($allIds)) {
                $eventCategories = Mage::getModel('enterprise_catalogevent/event')
                    ->getCollection()
                    ->addFieldToFilter('category_id', array('in' => $allIds))
                    ->addVisibilityFilter()
                    ->getColumnValues('category_id');

                $categories->addIdFilter($eventCategories);
            }
            foreach ($categories as $category) {
                 $this->_categories[] = $category;
            }
        }

        return $this->_categories;
    }

    /**
     * Retreive category url
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helper('catalog/category')->getCategoryUrl($category);
    }

    /**
     * Retrieve catalog category image url
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryImageUrl($category)
    {
        if ($category->getImageUrl()) {
            return $category->getImageUrl();
        }

        return false;
    }
}